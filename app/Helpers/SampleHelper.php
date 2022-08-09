<?php

namespace App\Helpers;


class SampleHelper{
    private static $count = 0;


    public static function addCount()
    {
        self::$count++;
    }

    public static function getCount()
    {
        return self::$count;
    }
}
