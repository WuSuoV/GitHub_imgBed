<?php
/*
 * QAQ HTTP服务核心
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/26
 */

namespace QAQ\Kernel;

class Http
{
    public static function Start()
    {
        //QAQ_Header
        header('X-Powered-By: QAQ_CORE_V' . QAQ_VERSION);
        //应用启动
        $res = App::run();
        if (is_array($res)) {
            header('Content-Type:text/json');
            echo json($res);
        } else if (is_string($res)) {
            echo $res;
        }
        //提高页面响应
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        //检查过期缓存
        Cache::CheckCache();
        //检查过期日志
        Log::CheckLog();
    }

    public static function GetPath()
    {
        return explode('?', $_SERVER['REQUEST_URI'])[0] ?? '/';
    }

    public static function RequestType()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}