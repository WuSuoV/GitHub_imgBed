<?php
/*
 * QAQ全局配置引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class Config
{
    public static function get($key)
    {
        global $config;
        if (isset($config[$key]) && !empty($config[$key])) return $config[$key];
        return false;
    }

    public static function set($key, $value)
    {
        global $config;
        $config[$key] = $value;
        if (isset($config[$key]) && !empty($config[$key])) return true;
        return false;
    }
}