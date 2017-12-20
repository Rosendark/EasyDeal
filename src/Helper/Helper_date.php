<?php
namespace App\Helper;
class Helper_date
{
    //il reste à verifier si les valeurs des dates sont bonnes

    public static function isValidDateTimeFR($date)
    {

        return preg_match("/^([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{4})$/",$date, $matches)
         && checkdate($matches[2], $matches[1], $matches[3]) ? true : false;
    }

    public static function conv_dateUS($date)
    {
        $str1 = str_replace('/', '-', $date); // On remplace les / par des -
        $str2 = strtotime($str1); // On récupère le timestamp de la date
        $ret = date('Y-m-d', $str2); // On convertit le timestamp à une date au format US

        return $ret; // On retourne cette date
    }

    public static function conv_dateFR($date="")
    {
        $str1 = str_replace('-', '/', $date); // On remplace les - par des /
        $str2 = strtotime($str1); // On récupère le timestamp de la date
        $ret         = date('d/m/Y', $str2); // On convertit le timestamp à une date au format US

        return $ret; // On retourne cette date
    }
}