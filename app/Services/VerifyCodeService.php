<?php

namespace App\Services;



use App\Library\Random;
use Gregwar\Captcha\CaptchaBuilder;
use App\Library\RedisFacade as Redis;

class VerifyCodeService
{
    /**
     * @param string $uuid
     * @param string $type
     * @return string
     */
    public static function generate(string $uuid, string $type = 'login') : string
    {
        $code = Random::randomString(4);
        Redis::setex(self::getRedisKey($uuid, $type), 600, $code);
        $builder = new CaptchaBuilder($code);
        $builder->setIgnoreAllEffects(true);
        $builder->setInterpolation(false);
        $builder->setBackgroundColor(255,255,255);
        $builder->setMaxFrontLines(0);
        $builder->build();

        return base64_encode($builder->get());
    }

    /**
     * @param string $uuid
     * @param string $type
     * @return string
     */
    private static function getRedisKey(string $uuid, string $type = 'login') : string
    {
        return 'verify:'.$uuid.':'.$type;
    }

    /**
     * @param string $uuid
     * @param string $verify
     * @param string $type
     * @return bool
     */
    public static function verify(string $uuid, string $verify, string $type = 'login') : bool
    {
        $code = strtolower(Redis::get(self::getRedisKey($uuid, $type)));
        if(empty($code) || $code != strtolower($verify)) {
            return false;
        }

        return true;
    }
}