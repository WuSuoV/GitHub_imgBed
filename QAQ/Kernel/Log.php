<?php
/*
 * QAQ日志记录
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/26
 */

namespace QAQ\Kernel;

class Log
{
    protected static $dir = QAQ_CORE_DIR . 'Storage/Log/';
    protected static $log_name;

    public static function init()
    {
        self::$log_name = self::$dir . 'Log_' . date('YmdH') . '.QAQ';
    }

    public static function Write($level, $msg)
    {
        self::init();
        $msg = date('Y-m-d H:i:s') . ' - [' . $level . '] - ' . $msg . PHP_EOL;
        File::MakeFile(self::$log_name, $msg, FILE_APPEND | LOCK_EX);
    }

    public static function Clear()
    {
        $dir = self::$dir;
        if (!file_exists($dir)) return true;
        return File::RmDir($dir);
    }

    public static function CheckLog()
    {
        $dir = self::$dir;
        $files = File::ReadDir($dir);
        if ($files) {
            foreach ($files as $file) {
                //获取日志保留时间戳
                $rm_time = (time() - (3600 * 24 * Config::get('log_save_day')));
                //删除保留时间前的日志
                if (@filemtime($file) && filemtime($file) < $rm_time) {
                    File::RmFile($file);
                }
            }
        }
    }
}