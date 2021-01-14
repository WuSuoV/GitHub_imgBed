<?php

namespace App\Model;

use QAQ\Kernel\Model;

class BlackIp extends Model
{
    protected $table = 'img_black_ip';
    protected $pk = 'id';

    public static function GetBlackIpByIp($ip)
    {
        return self::where(['ip' => $ip])->find();
    }

    public static function SetBlackIP($ip)
    {
        if (!self::GetBlackIpByIp($ip)) return self::insert([
            'ip' => $ip,
            'addtime' => time()
        ]);
    }

    public static function GetBlackIpList($page)
    {
        $size = 50;
        $list = self::order('addtime', 'desc')
            ->page($page, $size)
            ->select();
        if ($page != 1) {
            if (count($list) < 1) return [
                'code' => -1,
                'msg' => '没有更多了...'
            ];
        } else {
            if (count($list) < 1) return [
                'code' => 0
            ];
        }
        $count = self::count();
        if ($count % $size == 0) {
            $pages = $count / $size;
        } else {
            $pages = (int)($count / $size) + 1;
        }
        return [
            'code' => 1,
            'data' => $list,
            'count' => $count,
            'page' => $page,
            'pages' => $pages
        ];
    }

    public static function DelBlackIpById($id)
    {
        return self::where(['id' => $id])->delete();
    }
}