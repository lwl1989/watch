<?php
namespace App\Library;

use App\Exceptions\ErrorConstant;

class Cipher
{
    /**
     * 返回加密
     * @param $data
     * @param $key
     * @param $iv
     * @return string
     * @throws \Exception
     */
    public static function encrypt($data, $key, $iv)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $value = \openssl_encrypt(
            $data,
            env('OPEN_ENCRYPT_LEVEL'),
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($value === false) {
            throw new \Exception('Could not encrypt the data', ErrorConstant::OPEN_SERVER_RESPONSE_ENCRYPTION_ERROR);
        }

        return base64_encode($value);
    }

    /**
     * 請求解密
     * @param $data
     * @param $key
     * @param $iv
     * @return string
     * @throws \Exception
     */
    public static function decrypt($data, $key, $iv)
    {
        $data = base64_decode($data);

        $value = \openssl_decrypt(
            $data,
            env('OPEN_ENCRYPT_LEVEL'),
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($value === false) {
            throw new \Exception('Could not decrypt the data.', ErrorConstant::OPEN_CLIENT_REQUEST_DECRYPTION_ERROR);
        }

        return $value;
    }
}