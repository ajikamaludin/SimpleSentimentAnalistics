<?php
session_start();
$appDir = getcwd();
if (!file_exists($appDir."/vendor/autoload.php")) {
    die('vendor belum diinstall, lalukan #composer install ');
}
if (!file_exists($appDir."/lib/php-sentianalysis-id/autoload.php")) {
    die('php-sentianalysis-id tidak ditemukan ekstrak file lib/php-sentianalysis-id-master.zip ke lib/php-sentianalysis-id');
}

if (!file_exists($appDir."/env")) {
    die('file env tidak ditemukan');
}
include $appDir."/vendor/autoload.php";
include $appDir."/lib/php-sentianalysis-id/autoload.php";
include $appDir."/lib/LibEnv.php";
include $appDir."/lib/Helper.php";
include $appDir."/lib/CustomMysqli.php";
include $appDir."/lib/CleansingClass.php";

//Environment
$env = new LibEnv();

//Access Twitter Api
$twitter = new Twitter($_ENV['consumerKey'], $_ENV['consumerSecret'], $_ENV['accessToken'], $_ENV['accessTokenSecret']);

//Stemer
$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
$stemmer  = $stemmerFactory->createStemmer();

//Sentiment
$sentiment = new PHPInsight\Sentiment();

//Access Database
$db = new CustomMysqli($_ENV['db_host'], $_ENV['db_user'], $_ENV['db_password'], $_ENV['db_name']);
