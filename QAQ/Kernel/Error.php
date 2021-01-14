<?php
/*
 * QAQ错误处理
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class Error
{
    private static $errmap = [
        '0' => '致命错误',
        '1' => '运行时致命错误',
        '2' => '运行时警告',
        '4' => '编译语法错误',
        '8' => '运行时通知',
        '16' => '初始化致命错误',
        '32' => '初始化警告',
        '64' => '致命编译错误',
        '128' => '编译时警告',
        '256' => '用户自定义错误',
        '512' => '用户自定义警告',
        '1024' => '用户自定义通知',
        '2048' => 'PHP兼容性建议',
        '4096' => '可捕捉致命错误',
        '8192' => '运行时通知',
        '16384' => '用户产生的警告',
        '30719' => '其他警告WARNING',
        '-2000' => '找不到路由规则',
        '-2001' => '找不到模块',
        '-2002' => '找不到控制器',
        '-2003' => '找不到控制器方法',
    ];

    public static function appException($e)
    {
        $error = [];
        $error['code'] = $e->getCode();
        $error['level'] = self::$errmap[$e->getCode()] ?? $e->getCode();
        $error['message'] = $e->getMessage();
        $trace = $e->getTrace();
        if ('E' == $trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        $error['trace'] = $e->getTrace();
        //写日志
        $msg = $error['message'] . ' - 在' . $error['file'] . '第' . $error['line'] . '行';
        Log::Write($error['level'], $msg);
        if ($e->getCode() < -1999) {
            header('HTTP/1.1 404 Not Found', true, 404);
        } else {
            header('HTTP/1.1 500 Internal Server Error', true, 500);
        }
        self::halt($error);
    }

    public static function appError($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
                $msg = $errstr . ' - 在' . $errfile . '第' . $errline . '行';
                //写日志
                Log::Write('用户产生的警告', $msg);
                self::halt($msg);
                break;
            default:
                $msg = $errstr . ' - 在' . $errfile . '第' . $errline . '行';
                //写日志
                Log::Write('用户产生的警告', $msg);
                self::halt($msg);
                break;
        }
    }

    public static function fatalError()
    {
        if ($e = error_get_last()) {
            //写日志
            $msg = $e['message'] . ' - 在' . $e['file'] . '第' . $e['line'] . '行';
            $e['type'] = self::$errmap[$e['type']] ?? $e->getCode();
            Log::Write($e['type'], $msg);
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    self::halt($e);
                    break;
            }
        }
    }

    public static function halt($error)
    {
        if (DEBUG) {
            if (is_array($error)) {
                $codes = explode(PHP_EOL, file_get_contents($error['file']));
                $line = 1;
                $error['code'] = [];
                foreach ($codes as $code) {
                    if ($line >= $error['line'] - 6 && $line <= $error['line'] + 6) {
                        $code = [
                            'line' => $line,
                            'code' => str_replace(' ', '&nbsp;', htmlspecialchars($code)),
                        ];
                        if ($line == $error['line']) {
                            $code['error'] = true;
                        } else {
                            $code['error'] = false;
                        }
                        $error['code'][] = $code;
                    }
                    $line++;
                }
            }
            return error($error);
        } else {
            if (isset($error['code']) && $error['code'] < -1999) {
                return error([
                    'code' => 404,
                    'title' => '404 Not Found',
                    'msg' => 'Not Found'
                ], true);
            }
            return error([
                'code' => 500,
                'title' => '500 Server Error',
                'msg' => 'Server Error'
            ], true);
        }
    }
}