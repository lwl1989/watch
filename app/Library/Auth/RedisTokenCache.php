<?php

namespace App\Library\Auth;


use App\Models\Model;
use Illuminate\Support\Facades\Auth;
use App\Library\RedisFacade as Redis;

class RedisTokenCache
{
    public static function updateAttr(string $key, $value)
    {
        $md5 = md5(request()->getPassword());
        /**
         * @var $user Model
         */
        $user = Auth::user();
        $user->setAttribute($key, $value);
        //Redis::setex('user:login:'.$md5, 3600, json_encode($user->toArray()));
    }

    public static function clearAttr()
    {
        $md5 = md5(request()->getPassword());
        //Redis::del('user:login:'.$md5);
    }
}