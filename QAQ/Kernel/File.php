<?php
/*
 * QAQ文件操作引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/07/30
 */

namespace QAQ\Kernel;

class File
{
    public static function MakeDir($dir, $mode = 0777)
    {
        return is_dir($dir) or self::MakeDir(dirname($dir)) and @mkdir($dir, $mode);
    }

    public static function RmDir($dir)
    {
        if (!file_exists($dir)) return true;
        $dh = @opendir($dir);
        while ($file = @readdir($dh)) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                if (!is_dir($path)) {
                    @unlink($path);
                } else {
                    self::RmDir($dir);
                }
            }
        }
        @closedir($dh);
        if (@rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    public static function RmFile($dir)
    {
        if (!file_exists($dir)) return true;
        return @unlink($dir);
    }

    public static function MakeFile($dir, $data = '', $made = FILE_USE_INCLUDE_PATH)
    {
        $dirs = explode('/', $dir);
        $dirs = array_filter($dirs);
        $dirs = array_merge($dirs);
        //获取文件夹
        $new_dir = substr($dir, 0, strlen($dir) - strlen($dirs[count($dirs) - 1]) - 1);
        if (!file_exists($new_dir)) self::MakeDir($new_dir);
        return @file_put_contents($dir, $data, $made);
    }

    public static function ReadDir($dir)
    {
        if (!file_exists($dir)) return false;
        $dh = @opendir($dir);
        $data = [];
        while ($file = @readdir($dh)) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                $data[] = $path;
            }
        }
        @closedir($dh);
        return $data;
    }

    public static function ReadFile($dir)
    {
        if (!file_exists($dir)) return false;
        return file_get_contents($dir);
    }
}