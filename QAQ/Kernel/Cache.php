<?php
/*
 * QAQ缓存引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/26
 */

namespace QAQ\Kernel;

class Cache
{
    protected static $ttl_delimiter = '---QAQ_TTL---';

    private static function GetCacheDir()
    {
        return QAQ_CORE_DIR . 'Storage/Cache/';
    }

    public static function get($key)
    {
        $cache_dir = self::GetCacheDir();
        $file_name = $cache_dir . md5($key) . '.QAQ';
        $data = File::ReadFile($file_name);
        if ($data) {
            //检测是否带有有效期
            if (strpos($data, self::$ttl_delimiter) !== false) {
                $tmp = explode(self::$ttl_delimiter, $data);
                $ttl = $tmp[1];
                //是否过期
                if ($ttl < time()) return false;
                $data = @unserialize($tmp[0]);
            } else {
                $data = @unserialize($data);
            }
            if (!$data) return false;
            return $data;
        }
        return false;
    }

    public static function set($key, $value, $ttl = false)
    {
        $cache_dir = self::GetCacheDir();
        $file_name = $cache_dir . md5($key) . '.QAQ';
        if ($ttl) {
            if (!is_int($ttl)) {
                throw new \Exception('The ttl must to be int');
            }
            if ($ttl < 1) {
                throw new \Exception('The ttl must be greater than 0');
            }
            $data = @serialize($value) . self::$ttl_delimiter . (time() + $ttl);
        } else {
            $data = @serialize($value);
        }
        $res = File::MakeFile($file_name, $data);
        if ($res) return true;
        return false;
    }

    public static function clear($key)
    {
        $cache_dir = self::GetCacheDir();
        $file_name = $cache_dir . md5($key) . '.QAQ';
        return File::RmFile($file_name);
    }

    public static function ClearAllCache()
    {
        $dir = self::GetCacheDir();
        return File::RmDir($dir);
    }

    public static function CheckCache()
    {
        $dir = self::GetCacheDir();
        $files = File::ReadDir($dir);
        if ($files) {
            foreach ($files as $file) {
                $serialize = file_get_contents($file);
                //检测是否带有有效期
                if (strpos($serialize, self::$ttl_delimiter) !== false) {
                    $tmp = explode(self::$ttl_delimiter, $serialize);
                    $ttl = $tmp[1];
                    //是否过期
                    if ($ttl < time()) {
                        File::RmFile($file);
                    }
                }
            }
        }
    }
}