
## Simple Sentimen Analysis

Cara Kerja :
Aplikasi melakukan curl / pengambilan data twit melalui api twitter search / pencarian sesuai dengan keyword / hastag yang anda masukan melalui antar muka aplikasi kemudian hasil twit diklasifikasi merupakan cuitan bersentimen negatif atau positif

pengambilan data :
![SS Aplikasi](https://raw.githubusercontent.com/ajikamaludin/SimpleSentimentAnalistics/2c9f2a81aad2d03eabe9182f98fb072add765965/1.png)
analisis sentimen :
![SS Aplikasi 2](https://raw.githubusercontent.com/ajikamaludin/SimpleSentimentAnalistics/master/2.png)

status: work, last test 11/10/2021

aplikasi ini didukung dengan menggukaan library/pustaka :
1. PHP dg/Twitter-php https://github.com/dg/twitter-php
2. Sastrawi PHP Stemmer https://github.com/sastrawi/sastrawi
3. PHPID sentianalysis https://github.com/yasirutomo/php-sentianalysis-id

cara menjalankan aplikasi :

1. copy file `env.example` menjadi `env`
2. install depedencies dengan composer `composer install`
3. buat database dan import database `sentiment.sql`
4. edit file `env` untuk mengubah koneksi database, untuk `customer dan token api` twitter dapatkan dari https://developer.twitter.com/
5. ekstrak PHPID-sentianalysis `php-sentianalysis-id-master.zip` di directori `lib` ubah nama folder hasil ekstrak menjadi `php-sentianalysis-id`
6. aplikasi siap dijalankan dalam webserver atau juga dengan `php -S localhost:8000 -t .`

notes : 
- if something didn't work maybe something not support or not update anymore, please fix it by yourself.
- mencari nilai akurasi yang saya sarankan, buat dataset / list cuitann yang anda klasifikasikan sendiri secara manusiawi anda tentunya dapat paham sebuah kalimat mengandung nilai positif atau negatif , kemudian bandingkan data yang anda buat dengan cuitan yang diambil melalui aplikasi menggunakan metode cross validition , dengan metode ini anda dapat mencari nilai akurasi dari aplikasi ini sesuai dengan sentiment keyword /hastag yang anda gunakan.
- aplikasi ini hanya sekedar contoh yang saya gunakan untuk memenuhi tugas akhir mata kuliah , jika anda ingin menggunakannya untuk riset/projek atau sejenisnya silahkan boleh digunakan, diubah dan dilarang keras untuk memperjual belikan dengan mengambil sumber asli tanpa melakukan perubahan pada aplikasi.