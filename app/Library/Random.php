<?php


namespace App\Library;


use Ramsey\Uuid\Uuid;

class Random
{
        public static function randomInt(int $len)
        {
                $random = '';
                for ($i = 0; $i < $len; $i++) {
                        $random .= mt_rand(0,9);
                }
                return $random;
        }

        public static function randomString(int $len, string $factor = '')
        {
                $random = '';
                if($factor == '') {
                        $factor = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                }
                $strlen = strlen($factor);
                for ($i = 0; $i < $len; $i++) {
                        $random .= $factor[mt_rand(0, $strlen-1)];
                }
                return $random;
        }

        public static function randomUuid()
        {
        	return Uuid::uuid4()->toString();
        }
}