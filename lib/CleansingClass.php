<?php
/**
 * Class Clean
 * Maintainance By aji19kamaludin@gmail.com
 * created 31/10/2018
 * 
 * purpose : to clean tweet from curling tweet api
 * 
 */
class Clean{

    private $linkHttpRegex = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
    private $linkHttpsRegex = "@(http?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";
    private $hashtagRegex = "/#\S+\s*/";
    private $usernameRegex = "/(\s+|^)@\S+/";
    private $rtRegex = "/RT/";

    private $replaceWith = "";

    private $textClean = "";

    public function __toString()
    {
        return $this->textClean;
    }

    public function toString()
    {
        return $this->textClean;
    }

    public function __construct($text, $replaceWith = "")
    {
        $this->replaceWith = $replaceWith;
        $this->textClean = $this->cleanHashtag($text);
        $this->textClean = $this->cleanLink($this->textClean);
        $this->textClean = $this->cleanRt($this->textClean);
        $this->textClean = $this->cleanUsername($this->textClean);
    }

    public function cleanLink($text){
        $text = preg_replace($this->linkHttpsRegex, $this->replaceWith, $text);
        $text = preg_replace($this->linkHttpRegex, $this->replaceWith, $text);
        return $text;
    }

    public function cleanHashtag($text){
        return preg_replace($this->hashtagRegex, $this->replaceWith, $text);
    }

    public function cleanUsername($text){
        return preg_replace($this->usernameRegex, $this->replaceWith, $text);
    }

    public function cleanRt($text){
        return preg_replace($this->rtRegex, $this->replaceWith, $text);
    }
}