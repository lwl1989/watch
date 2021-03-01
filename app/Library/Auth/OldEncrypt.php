<?php

namespace App\Library\Auth;


class OldEncrypt
{
    const KEY = "c00c06c60bbf69afd7b4b342ec62cdbc";
    CONST IV = "f7a20e28ebaf0407";
    /**
     * AES/CBC/PKCS5Padding Encrypter
     *
     * @param $str
     * @return string
     */
    public static function encrypt($str)
    {
        $en = openssl_encrypt($str, 'AES-256-CBC', self::KEY, OPENSSL_RAW_DATA, self::IV);
        return bin2hex($en);
    }

    /**
     * AES/CBC/PKCS5Padding Decrypter
     *
     * @param $encryptedStr
     * @return string
     */
    public static function decrypt($encryptedStr)
    {
        return openssl_decrypt(hex2bin($encryptedStr), 'AES-256-CBC', self::KEY, OPENSSL_RAW_DATA, self::IV);
    }
}