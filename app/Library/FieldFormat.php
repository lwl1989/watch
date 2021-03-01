<?php

namespace App\Library;



class FieldFormat
{
    public static function hideLength(string $value, int $len = 10) : string
    {
        return mb_substr($value, 0, $len);
    }

    public static function cameMark(string $value) : string
    {
        return StrParse::underToCame($value);
    }



    public static function strval($value) : string
    {
        return self::strval($value);
    }

    public static function jsonDecode($value) : array
    {
        if(is_array($value)) {
            return $value;
        }

        if(is_string($value)) {
            $value = json_decode($value, true);
        }

        if(!is_array($value)) {
            return [];
        }

        return $value;
    }
}