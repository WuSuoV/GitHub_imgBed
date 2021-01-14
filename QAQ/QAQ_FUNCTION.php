<?php
/*
 * QAQ核心函数
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

if (!function_exists('dump')) {
    function dump()
    {
        $vars = func_get_args();
        if (count($vars) > 0) {
            foreach ($vars as $var) {
                echo '<pre>';
                var_dump($var);
                echo '</pre>';
            }
        }
    }
}

if (!function_exists('error')) {
    function error($error, $Simple = false)
    {
        $config = [
            'view_path' => QAQ_CORE_DIR . 'QAQ/Tpl/',
            'cache_path' => QAQ_CORE_DIR . 'Storage/Temp/',
            'view_suffix' => 'html',
        ];
        \QAQ\Kernel\View::config($config);
        if ($Simple) die(\QAQ\Kernel\View::fetch('SimpleError', $error));
        die(\QAQ\Kernel\View::fetch('Error', ['error' => $error]));
    }
}

if (!function_exists('config')) {
    function config($key, $value = 0)
    {
        if (!$value) {
            $config = \QAQ\Kernel\Config::get($key);
            if (isset($config)) return $config;
            return null;
        } else {
            return \QAQ\Kernel\Config::set($key, $value);
        }
    }
}

if (!function_exists('request')) {
    function request()
    {
        return \QAQ\Kernel\Request::instance();
    }
}

if (!function_exists('view')) {
    function view($path, $vars = [])
    {
        return \QAQ\Kernel\View::fetch($path, $vars);
    }
}

if (!function_exists('verify_data')) {
    function verify_data($data, $key, $s = 0)
    {
        if ($s) {
            if (isset($data[$key]) && !empty($data[$key])) return true;
            return false;
        } else {
            if (isset($data[$key])) return true;
            return false;
        }
    }
}

if (!function_exists('json')) {
    function json($vars)
    {
        return json_encode($vars, JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $is_301 = false)
    {
        return \QAQ\Kernel\Request::redirect($url, $is_301);
    }
}

if (!function_exists('jump')) {
    function jump()
    {
        return \QAQ\Kernel\Jump::instance();
    }
}

if (!function_exists('str_replace_once')) {
    function str_replace_once($needle, $replace, $haystack)
    {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return $haystack;
        }
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }
}