<?php 

namespace App\Http\Traits;

trait Utils
{
    static function addLeadingZero($value) {
        return str_pad($value, 2, '0', STR_PAD_LEFT);
    }

    static function hasKey($array, $key) {
        return array_key_exists($key, $array);
    }

    static function hasProp($object, $prop) {
        return property_exists($object, $prop);
    }
    
    static function randomColor() {
        return sprintf('#%06X', mt_rand(0, 0xBBBBBB));
    }

    static function datesPast($num){
        $dates = array();

        $month = date("m");
        $day = date("d");
        $year = date("Y");

        for($i = 0; $i <= $num; $i++){
            $dates[] = date('Y-m-d', mktime(0, 0, 0, $month, ($day - $i), $year));
        }
        return $dates;
    }

    static function monthsPast($num){
        
        $months = array();

        $month = date("m");
        $year = date("Y");

        for($i = 0; $i <= $num; $i++){
            $months[] = date('Y-m-d', mktime(0, 0, 0, ($month - $i), 1, $year));
        }
        return $months;
    }

    static function generateRandom($length){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = \mb_strlen($chars, '8bit') - 1;
        for($i = 0; $i < $length; $i++){
            $pieces[] = $chars[random_int(0, $max)];
        }
        return \implode('', $pieces);
    }

    static function generateUUID() {
        return implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 4));
    }

    static function datesFromIntervals($start, $end){

        $startTime = strtotime($start);
        $endTime = strtotime($end);

        $num = round(($endTime - $startTime) / (60 * 60 * 24));

        $dates = array();

        $month = date("m", strtotime($end));
        $day = date("d", strtotime($end));
        $year = date("Y", strtotime($end));

        for($i = 0; $i <= $num; $i++){
            $dates[] = date('Y-m-d', mktime(0, 0, 0, $month, ($day - $i), $year));
        }
        
        return array_reverse($dates);
    }
}
