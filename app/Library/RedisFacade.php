<?php
/**
 * Created by Wenlong.
 * Date: 2019/2/2
 * Time: 12:07
 */

namespace App\Library;


use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Redis;
use Predis\Pipeline\Pipeline;
use Psy\Exception\RuntimeException;

class RedisFacade extends Redis
{
    public static $db = 0;
    public static $dbs = [];
//
    public static function select($db) {
//        self::$dbs[] = $db;
//        if($do) {
//
//        }
//        var_dump(self::$db, $db);
        if(self::$db != $db) {
            $instance = static::getFacadeRoot();
            if (! $instance) {
                throw new RuntimeException('A facade root has not been set.');
            }
            $instance->select($db);
            self::$db = $db;
        }
    }

    public static function pipeline(callable $callable = null)
    {
        /** @var RedisManager $instance */
        $instance = static::getFacadeRoot();
        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        if(env('REDIS_CLIENT','predis') == 'phpredis') {
            $pipeline = RedisPipeline::getInstance($instance->client());

            return $pipeline->execute($callable);
        }

        return parent::pipeline($callable);
    }

    public static function transaction(callable $callable = null)
    {
        /** @var RedisManager $instance */
        $instance = static::getFacadeRoot();
        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        if(env('REDIS_CLIENT','predis') == 'phpredis') {
            $pipeline = RedisPipeline::getInstance($instance->client(), true);

            return $pipeline->execute($callable);
        }

        return parent::pipeline($callable);
    }


    public static function exec()
    {
        /** @var RedisManager $instance */
        $instance = static::getFacadeRoot();
        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        if(env('REDIS_CLIENT','predis') == 'phpredis') {
            $pipeline = RedisPipeline::getInstance($instance->client(), true);

            return $pipeline->exec();
        }

        return parent::exec();
    }
    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
//    public static function __callStatic($method, $args)
//    {
//
////            var_dump($method);
//                $instance = static::getFacadeRoot();
//
//                if (!$instance) {
//                    throw new RuntimeException('A facade root has not been set.');
//                }
//                return $instance->$method(...$args);
//            //}
////        }
//    }
}