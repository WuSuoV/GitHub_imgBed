<?php
/*
 * QAQ HTTP请求引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class Request
{
    public static function instance()
    {
        return new self();
    }

    public static function get($key = 0, $default = 0)
    {
        if (!$key) {
            return $_GET;
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        } else {
            if ($default) return $default;
            return false;
        }
    }

    public static function post($key = 0, $default = 0)
    {
        if (!$key) {
            return $_POST;
        }
        if (isset($_POST[$key])) {
            return $_POST[$key];
        } else {
            if ($default) return $default;
            return false;
        }
    }

    public static function redirect($url, $is_301 = false)
    {
        if ($is_301) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        header('Location: ' . $url);
        return true;
    }

    public static function IsAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
        return false;
    }

    public static function IsGet()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') return true;
        return false;
    }

    public static function IsPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') return true;
        return false;
    }
}