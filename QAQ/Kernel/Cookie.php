<?php
/*
 * QAQ Cookie引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class Cookie
{
    public static function set($key, $value, $path = '/', $ttl = 0)
    {
        return setcookie($key, $value, time() + $ttl, $path);
    }

    public static function get($key)
    {
        if (isset($_COOKIE[$key])) return $_COOKIE[$key];
        return false;
    }

    public static function del($key, $path = '/')
    {
        unset($_COOKIE[$key]);
        return setcookie($key, null, time() - 1, $path);
    }
}