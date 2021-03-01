<?php

namespace App\Traits;

use App\Classes\mailAttach;
use Illuminate\Support\Facades\Mail;

trait getFileFromMail
{
    public function get_data_from_mail()
    {
        header('Content-Type: text/html; charset=utf-8');

        //Connection Details
        $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
        $username = 'bookingwhizz26@gmail.com'; //change this
        $password = 'n3gativ3'; //change this

        //Search parameters
        //See http://uk3.php.net/manual/en/function.imap-search.php for possible keys
        //SINCE date should be in j F Y format, e.g. 9 August 2013
        $searchArray = array('SUBJECT' => 'Test', 'SINCE' => date('j F Y', strtotime('1 month ago')));

        //Save attachment file to
        $saveToPath = storage_path() . "/app/public/"; //change this
        //Extract zip files to
        $unzipDest = storage_path() . "/app/public/"; //change this

        //Create an object
        $xa = new mailAttach($hostname, $username, $password, false);
        $xa->get_files($searchArray, $saveToPath);

        $xa->extract_zip_to($unzipDest);
        $xaA = (array)$xa;
        $file = $xaA["\x00App\Classes\mailAttach\x00zips"][0];
        return $file;
    }

    public function get_data_from_yandex_mail()
    {
        header('Content-Type: text/html; charset=utf-8');

        //Connection Details
        $hostname = '{imap.yandex.ru:993/imap/ssl}INBOX';
        $username = 'pmsreports@ybookingwhizz.co'; //change this
        $password = 'Beta2020!'; //change this

        //Search parameters
        //See http://uk3.php.net/manual/en/function.imap-search.php for possible keys
        //SINCE date should be in j F Y format, e.g. 9 August 2013
        $searchArray = array('SUBJECT' => 'Test', 'SINCE' => date('j F Y', strtotime('1 month ago')));

        //Save attachment file to
        $saveToPath = storage_path() . "/app/public/"; //change this
        //Extract zip files to
        $unzipDest = storage_path() . "/app/public/"; //change this

        //Create an object
        $xa = new mailAttach($hostname, $username, $password, false);
        $xa->get_files($searchArray, $saveToPath);
        $xa->extract_zip_to($unzipDest);

        $xaA = (array)$xa;
        $filePath = $xaA["\x00App\Classes\mailAttach\x00path"];
        $file = $xaA["\x00App\Classes\mailAttach\x00attachments"][1]['filename'];
        $path = $filePath . $file;

        return $path;
    }
}
