<?php

namespace App\Service;

use QAQ\Kernel\Cache;
use QAQ\Kernel\Config;
use QAQ\Kernel\Db;
use QAQ\Kernel\Log;

class Update
{
    public static function CheckVersion()
    {
        $version = Config::get('version');
        $url = 'http://api.yyhy.me/img_bed_version.php?version=' . $version;
        $res = httpGet($url);
        if (!$res) return [
            'code' => -1,
            'msg' => '更新服务器开小差了~'
        ];
        $res = json_decode($res, true);
        if ($res['code'] == 1) return $res;
        return [
            'code' => -1,
            'msg' => '您已是最新版本V' . $version
        ];
    }

    public static function DoUpdate()
    {
        $version = Config::get('version');
        $url = 'http://api.yyhy.me/img_bed_version.php?version=' . $version;
        $res = httpGet($url);
        if (!$res) return [
            'code' => -1,
            'msg' => '更新服务器开小差了~'
        ];
        $res = json_decode($res, true);
        if ($res['code'] != 1) return [
            'code' => -1,
            'msg' => '您已是最新本，无需升级'
        ];
        //下载更新包
        $update_zip = httpGet($res['update_zip']);
        if (!$update_zip) return [
            'code' => -1,
            'msg' => '下载更新包失败，请重试！'
        ];
        //放到项目根目录
        try {
            file_put_contents(QAQ_CORE_DIR . 'update.zip', $update_zip);
        } catch (\Exception $e) {
            return [
                'code' => -1,
                'msg' => '存储更新包时发生错误，请检查文件权限！'
            ];
        }
        //检查是否有zip拓展类
        if (!class_exists('ZipArchive')) return [
            'code' => -1,
            'msg' => '没有ZipArchive拓展类，请手动升级！'
        ];
        //文件路径
        $file = QAQ_CORE_DIR . 'update.zip';
        //开始解压
        $zip = new \ZipArchive();
        $openRes = $zip->open($file);
        if ($openRes !== TRUE) return [
            'code' => -1,
            'msg' => '解压升级包失败，请手动升级！'
        ];
        //解压到根目录
        $zip->extractTo(QAQ_CORE_DIR);
        $zip->close();
        //判断是否有sql更新
        if ($res['update_sql']) {
            $sqls = explode(';', $res['update_sql']);
            $sqls = array_filter($sqls);
            foreach ($sqls as $sql) {
                $sql = $sql . ';';
                try {
                    Db::execute($sql);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        //清空缓存避免出现问题
        Cache::ClearAllCache() && Log::Clear();
        //升级完毕
        return [
            'code' => 1,
            'msg' => '升级成功！'
        ];
    }
}