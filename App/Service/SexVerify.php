<?php

namespace App\Service;

use App\Model\BlackIp;
use App\Model\Img;
use QAQ\Kernel\Cache;

class SexVerify
{
    //提交一个鉴黄任务
    public static function SubmitAWork()
    {
        if (!config('sex_verify_sw')) return [
            'code' => -1,
            'msg' => '未开启鉴黄！'
        ];
        if (!config('sex_verify_token') || !config('sex_verify_level')) return [
            'code' => -1,
            'msg' => '未配置鉴黄！'
        ];
        $ip = rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);
        $UserAgent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)";
        $headers = ['X-FORWARDED-FOR:' . $ip . '', 'CLIENT-IP:' . $ip . ''];
        $ch = curl_init();
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        curl_setopt($ch, CURLOPT_URL, $http_type . $_SERVER['HTTP_HOST'] . '/api/async_sex_verify/' . GetSyskey());
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $UserAgent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
        return [
            'code' => 1,
            'msg' => '提交鉴黄任务成功！'
        ];
    }

    //执行鉴黄
    public static function DoVerify()
    {
        $list = Img::where([
            'sex_verify' => 0
        ])->order('img_id', 'desc')->limit(5)->select();
        foreach ($list as $v) {
            if (self::VerifyAImg($v['img_id'], $v['url'])) {
                //线程检测
                if (Cache::get('img_verify_' . $v['img_id'])) {
                    continue;
                }
                //是否已传到Github
                if (strpos($v['url'], 'cdn.jsdelivr.net') === false) {
                    continue;
                }
                //设置运行标识
                Cache::set('img_verify_' . $v['img_id'], 1, 60);
                //是否开启拉黑IP
                if (config('sex_verify_black_ip')) {
                    BlackIp::SetBlackIP($v['ip']);
                }
                //逻辑处理
                if (config('sex_verify_config') == 0) {
                    Img::del_img($v['img_id']);
                } else {
                    Img::where([
                        'img_id' => $v['img_id']
                    ])->update([
                        'sex_verify' => 2
                    ]);
                }
            }
        }
    }

    //鉴定一张图片
    public static function VerifyAImg($id, $url)
    {
        $url = 'https://api.moderatecontent.com/moderate/?key=' . config('sex_verify_token') . '&url=' . $url;
        $res = httpGet($url);
        $res = json_decode($res, 1);
        if (isset($res['rating_letter'])) {
            Img::where([
                'img_id' => $id
            ])->update([
                'sex_verify' => 1
            ]);
            //是黄图
            if (self::CompareLevel($res['rating_letter'])) return true;
        }
        return false;
    }

    //等级是否超出设定等级
    public static function CompareLevel($level)
    {
        //等级集合
        $levels = array_keys(GetSexLevel());
        //系统设定的等级
        $config_level = config('sex_verify_level') - 1;
        for ($i = 0; $i < 3; $i++) {
            //图片的等级
            if ($level == $levels[$i]) {
                $level = $i;
            }
        }
        if ($level > $config_level) return true;
        return false;
    }
}