<?php
/*
 * QAQ页面跳转组件
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/26
 */

namespace QAQ\Kernel;

class Jump
{
    protected static $config = [
        'view_path' => QAQ_CORE_DIR . 'QAQ/Tpl/',
        'cache_path' => QAQ_CORE_DIR . 'Storage/Temp/',
        'view_suffix' => 'html',
    ];
    protected static $time = 3;
    protected static $url = 'javascript:void(history.back())';

    public static function instance()
    {
        return new self();
    }

    public static function error($msg, $url = false, $time = false)
    {
        if (!$url) {
            $url = self::$url;
        }
        if (!$time) {
            $time = self::$time;
        }
        $info = [
            'msg' => $msg,
            'url' => $url,
            'time' => $time,
            'type' => 'error'
        ];
        \QAQ\Kernel\View::config(self::$config);
        die(View::fetch('Jump', $info));
    }

    public static function success($msg, $url = false, $time = false)
    {
        if (!$url) {
            $url = self::$url;
        }
        if (!$time) {
            $time = self::$time;
        }
        $info = [
            'msg' => $msg,
            'url' => $url,
            'time' => $time,
            'type' => 'success'
        ];
        \QAQ\Kernel\View::config(self::$config);
        die(View::fetch('Jump', $info));
    }

    public static function info($msg, $url = false, $time = false)
    {
        if (!$url) {
            $url = self::$url;
        }
        if (!$time) {
            $time = self::$time;
        }
        $info = [
            'msg' => $msg,
            'url' => $url,
            'time' => $time,
            'type' => 'info'
        ];
        \QAQ\Kernel\View::config(self::$config);
        die(View::fetch('Jump', $info));
    }
}