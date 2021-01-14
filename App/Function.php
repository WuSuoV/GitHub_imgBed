<?php
/*
 * 应用公共文件
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

//获取真实IP
function real_ip()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] as $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
}

//模拟HTTP GET请求
function httpGet($url)
{
    $ip = rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);
    $UserAgent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)";
    $headers = ['X-FORWARDED-FOR:' . $ip . '', 'CLIENT-IP:' . $ip . ''];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT, $UserAgent);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function isMobile()
{
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = ['nokia', 'sony', 'ericsson', 'mot',
            'samsung', 'htc', 'sgh', 'lg', 'sharp',
            'sie-', 'philips', 'panasonic', 'alcatel',
            'lenovo', 'iphone', 'ipod', 'blackberry',
            'meizu', 'android', 'netfront', 'symbian',
            'ucweb', 'windowsce', 'palm', 'operamini',
            'operamobi', 'openwave', 'nexusone', 'cldc',
            'midp', 'wap', 'mobile'
        ];
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function get_api($type)
{
    if ($type == 1) return 'https://api.github.com/';
    if ($type == 2) return 'http://api.github.kkpp.cc/';
    if ($type == 3) return 'https://v2.kkpp.cc/';
    return 'https://api.github.com/';
}

function GetSyskey()
{
    return md5($_SERVER['HTTP_HOST'] . json_encode(include 'DataBase.php'));
}

function GetFileSize($num)
{
    $p = 0;
    $format = 'bytes';
    if ($num > 0 && $num < 1024) {
        $p = 0;
        return number_format($num) . ' ' . $format;
    }
    if ($num >= 1024 && $num < pow(1024, 2)) {
        $p = 1;
        $format = 'KB';
    }
    if ($num >= pow(1024, 2) && $num < pow(1024, 3)) {
        $p = 2;
        $format = 'MB';
    }
    if ($num >= pow(1024, 3) && $num < pow(1024, 4)) {
        $p = 3;
        $format = 'GB';
    }
    if ($num >= pow(1024, 4) && $num < pow(1024, 5)) {
        $p = 3;
        $format = 'TB';
    }
    $num /= pow(1024, $p);
    return number_format($num, 3) . ' ' . $format;
}

//返回鉴黄等级
function GetSexLevel()
{
    return [
        'e' => '所有人',
        't' => '青少年',
        'a' => '限制级'
    ];
}

function GetHttpType()
{
    return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
}

//获取用户访问ID
function GetUserVisitId()
{
    return md5($_SERVER['REMOTE_ADDR']);
}

//获取QQ头像
function QQImg($qq)
{
    return 'https://q4.qlogo.cn/headimg_dl?dst_uin=' . $qq . '&spec=640';
}

//是否是QQ
function IsQQ($qq)
{
    if (!is_numeric($qq) || strlen($qq) > 10 || strlen($qq) < 5) return false;
    return true;
}

//用户名检查
function CheckUserName($UserName)
{
    if (strlen($UserName) > 12 || strlen($UserName) < 4) return false;
    return true;
}

//密码检查
function CheckPassword($Password)
{
    if (strlen($Password) > 12 || strlen($Password) < 6) return false;
    return true;
}

/*
 * 模拟HttpPost请求
 */
function httpPost($url,$data){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

/*
 * 返回违规提示图片
 */
function SexErrorImage()
{
    return 'data:image/png;base64,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';
}
