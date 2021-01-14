<?php
/*
 * QAQ Redis引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class Redis
{
    protected $redis;

    public function __construct()
    {
        if (!class_exists('Redis')) {
            throw new \Exception('Redis扩展不存在！');
        }
        $this->redis = new \Redis();
        $this->redis->connect(config('redis_host'), config('redis_port'));
        $this->redis->auth(config('redis_password'));
    }

    //增
    public static function set($key, $value, $time = 0)
    {
        $static = new static();
        if ($time) {
            return $static->redis->setex($key, $time, $value);
        } else {
            return $static->redis->set($key, $value);
        }
    }

    //查
    public static function get($key)
    {
        $static = new static();
        return $static->redis->get($key);
    }

    //删
    public static function delete()
    {
        $vars = func_get_args();
        if (count($vars) > 0) {
            $static = new static();
            return $static->redis->del($vars);
        }
        return false;
    }
}