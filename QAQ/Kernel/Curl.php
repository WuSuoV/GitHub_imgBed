<?php
/*
 * QAQ Curl引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class Curl
{
    public static function GetCurl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $header = [
            'Accept:*/*',
            'Accept-Encoding:gzip,deflate,sdch',
            'Accept-Language:zh-CN,zh;q=0.8'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
        }
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if ($referer) {
            if ($referer == 1) {
                curl_setopt($ch, CURLOPT_REFERER, $url);
            } else {
                curl_setopt($ch, CURLOPT_REFERER, $referer);
            }
        }
        if ($ua) {
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36');
        }
        if ($nobaody) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            //主要头部
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //跟随重定向
        }
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    public static function GetMultiCurl($urls)
    {
        $count = count($urls);
        $data = [];
        $chs = [];
        // 创建批处理cURL句柄
        $mh = curl_multi_init();
        // 创建cURL资源
        for ($i = 0; $i < $count; $i++) {
            $chs[$i] = curl_init();
            curl_setopt($chs[$i], CURLOPT_URL, $urls[$i]['url']);
            curl_setopt($chs[$i], CURLOPT_TIMEOUT, 60);
            curl_setopt($chs[$i], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($chs[$i], CURLOPT_SSL_VERIFYHOST, false);
            $header = [
                'Accept:*/*',
                'Accept-Encoding:gzip,deflate,sdch',
                'Accept-Language:zh-CN,zh;q=0.8'
            ];
            curl_setopt($chs[$i], CURLOPT_HTTPHEADER, $header);
            if (!empty($urls[$i]['param'])) {
                curl_setopt($chs[$i], CURLOPT_POST, 1);
                curl_setopt($chs[$i], CURLOPT_POSTFIELDS, $urls[$i]['param']);
            }
            if (!empty($urls[$i]['header'])) {
                curl_setopt($chs[$i], CURLOPT_HEADER, TRUE);
            }
            if (!empty($urls[$i]['cookie'])) {
                curl_setopt($chs[$i], CURLOPT_COOKIE, $urls[$i]['cookie']);
            }
            if (!empty($urls[$i]['referer'])) {
                if ($urls[$i]['referer'] == 1) {
                    curl_setopt($chs[$i], CURLOPT_REFERER, $urls[$i]['url']);
                } else {
                    curl_setopt($chs[$i], CURLOPT_REFERER, $urls[$i]['referer']);
                }
            }
            if (!empty($urls[$i]['ua'])) {
                curl_setopt($chs[$i], CURLOPT_USERAGENT, $urls[$i]['ua']);
            } else {
                curl_setopt($chs[$i], CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36');
            }
            if (!empty($urls[$i]['nobody'])) {
                curl_setopt($chs[$i], CURLOPT_NOBODY, 1);
                //主要头部
                curl_setopt($chs[$i], CURLOPT_FOLLOWLOCATION, true); //跟随重定向
            }
            curl_setopt($chs[$i], CURLOPT_ENCODING, "gzip");
            curl_setopt($chs[$i], CURLOPT_RETURNTRANSFER, 1);
            curl_multi_add_handle($mh, $chs[$i]);
        }
        // 增加句柄
        for ($i = 0; $i < $count; $i++) {
            curl_multi_add_handle($mh, $chs[$i]);
        }
        // 执行批处理句柄
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($active > 0);
        while ($active and $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        for ($i = 0; $i < $count; $i++) {
            $content = curl_multi_getcontent($chs[$i]);
            $data[$i] = (curl_errno($chs[$i]) == 0) ? $content : false;
        }
        // 关闭全部句柄
        for ($i = 0; $i < $count; $i++) {
            curl_multi_remove_handle($mh, $chs[$i]);
        }
        curl_multi_close($mh);
        return $data;
    }
}
