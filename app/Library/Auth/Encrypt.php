<?php

namespace App\Library\Auth;


use App\Exceptions\ErrorConstant;
use App\Models\RegisterUsers\Users;
use Ramsey\Uuid\Uuid;

class Encrypt
{
    static $CURRENT_UUID = '';

    public static function getVisitorResult(array $data = []) : array
    {
        $data['uid'] = 0;
        $data['device_uuid'] = $data['device_uuid'] ?? Uuid::uuid4()->toString();
        $data['visitor'] = true;
        try{
            $token = self::generateToken($data);
        } catch (\Exception $exception) {
            return ['code' => ErrorConstant::SYSTEM_ERR, 'response' => $exception->getMessage()];
        }
        return ['token' => $token, 'device_uuid' => $data['device_uuid']];
    }

    public static function getLoginResult(array $data): array
    {
        try {
            if (!isset($data['uid'])) {
                throw new \Exception('ReGenerateToken lost params uid', ErrorConstant::SYSTEM_ERR);
            }
            $token = self::generateToken($data);
        } catch (\Exception $exception) {
            return ['code' => ErrorConstant::SYSTEM_ERR, 'response' => $exception->getMessage()];
        }
        return ['token' => $token, 'device_uuid' => $data['device_uuid']];
    }

    /**
     * @param array $data [uid bid t uuid]
     * @throws \Exception
     * @return string
     */
    public static function generateToken(array $data): string
    {
        if (!isset($data['uid'])) {
            throw new \Exception('ReGenerateToken lost params uid', ErrorConstant::SYSTEM_ERR);
        }
        if (!isset($data['device_uuid']) or empty($data['device_uuid'])) {
            $uuid = Uuid::uuid4()->toString();
            $data['device_uuid'] = $uuid;
        }
        self::$CURRENT_UUID = $data['device_uuid'];
        $data['time'] = time();
        $enc = self::encrypt($data);
        $token = strtr($enc, '+/=', '_- ');
        return $token;
    }


    /**
     * @param string $token
     * @param bool $throw
     * @return array
     * @throws \Exception
     */
    public static function auth(string $token, bool $throw = true): array
    {
        if (empty($token)) {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Basic realm="' . $_SERVER['HTTP_HOST'] . '"');
            throw new \Exception('Authentication failed', ErrorConstant::DATA_ERR);
        }

        $token = strtr($token, '_-', '+/');
        $data = self::decrypt($token);
        if (intval($data['uid']) == 0) {
            if($throw) {
                throw new \Exception('Authentication failed', ErrorConstant::DATA_ERR);
            }
        }

        return $data;
    }

    /**
     * @param string $token
     * @return array
     * @throws \Exception
     */
    public static function ReGenerateToken(string $token): array
    {
        $data = self::decrypt($token);

        $token = self::generateToken($data);
        $data['token'] = $token;
        $data['needUpdateToken'] = true;
        return $data;
    }


    /**
     * 加密。把數組訊息進行加密
     * @param array $user
     * @return string
     * @throws \Exception
     */
    private static function encrypt(array $user): string
    {
        $string = json_encode($user);
        list($level, $key, $iv) = self::getIv();
        $pass = openssl_encrypt($string, $level, $key, OPENSSL_RAW_DATA, $iv);

        $ret = base64_encode($pass);
        return $ret;
    }

    /**
     * 解密 返回的是加密之前的內容。
     * 或者在加密端先urldecode
     * @param string $oldPass
     * @return array
     * @throws \Exception
     */
    private static function decrypt(string $oldPass): array
    {
        list($level, $key, $iv) = self::getIv();
        $pass = base64_decode($oldPass);
        $pass = openssl_decrypt($pass, $level, $key, OPENSSL_RAW_DATA, $iv);

        if (!$pass) {
            throw new \Exception('字符串解碼失敗:' . $oldPass, ErrorConstant::SYSTEM_ERR);
        }

        $ret = trim($pass);
        $user = json_decode($ret, true);
        if (empty($user)) {
            $user = [];
        }
        return $user;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private static function getIv(): array
    {
        $key = env('AUTH_ENCRYPT_KEY', 'ttpush');
        $level = env('AUTH_ENCRYPT_LEVEL', 'aes-128-cbc');
        $iv = env('AUTH_ENCRYPT_IV');
        if ($iv == '') {
            if (strpos($level, '128') !== false or strpos($level, '256') !== false) {
                $iv = 'cm80109410-12345';
            } else {
                throw new \Exception('not support this encrypt', ErrorConstant::SYSTEM_ERR);
            }
        }

        return [$level, $key, $iv];
    }
}
