<?php

namespace App\Model;

use App\Service\Github;
use QAQ\Kernel\Model;

class Img extends Model
{
    protected $table = 'img_imgs';
    protected $pk = 'img_id';

    public static function GetCountByUid($uid)
    {
        return self::where([
            'uid' => $uid
        ])->count();
    }

    public static function DelImgByIdWithUid($uid, $id)
    {
        $info = self::where([
            'img_id' => $id,
            'uid' => $uid
        ])->find();
        if (strpos($info['url'], 'cdn.jsdelivr.net') !== false) {
            $github_config = [
                'USER' => config('USER'),
                'REPO' => config('REPO'),
                'MAIL' => config('MAIL'),
                'TOKEN' => config('TOKEN')
            ];
            Github::del_file($info['img_name'], $info['sha'], $github_config);
        }
        return self::where([
            'uid' => $uid,
            'img_id' => $id
        ])->delete();
    }

    public static function GetPreByUid($uid)
    {
        $size = self::where([
            'uid' => $uid
        ])->sum('size');
        return GetFileSize($size);
    }

    public static function home_img_list($page)
    {
        $size = 50;
        $list = self::order('addtime', 'desc')
            ->page($page, $size)
            ->select();
        if (count($list) < 1) return [
            'code' => -1,
            'msg' => '没有更多了...'
        ];
        $img_list = [];
        foreach ($list as $k => $v) {
            $url = GetHttpType() . $_SERVER['HTTP_HOST'] . '/img/' . $v['img_id'];
            if ($v['sex_verify'] == 2) {
                $url = SexErrorImage();
            }
            $v['ip'] = explode('.', $v['ip']);
            $ip = $v['ip'][0] . '.' . $v['ip'][1] . '.***.' . $v['ip'][3];
            $img_list[] = [
                'url' => $url,
                'ip' => $ip,
                'addtime' => $v['addtime']
            ];
        }
        $count = self::count();
        return [
            'code' => 1,
            'data' => $img_list,
            'count' => $count,
            'page' => $page,
            'pages' => round($count / $size, 0)
        ];
    }

    public static function user_img_list($uid, $page)
    {
        $size = 50;
        $list = self::order('addtime', 'desc')
            ->where([
                'uid' => $uid
            ])
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
        foreach ($list as $k => $v) {
            $url = GetHttpType() . $_SERVER['HTTP_HOST'] . '/img/' . $v['img_id'];
            $list[$k]['url'] = $url;
        }
        $count = self::where([
            'uid' => $uid
        ])->count();
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

    public function GetInfoById($id)
    {
        return self::where([
            'img_id' => $id
        ])->find();
    }

    public static function admin_img_list($page)
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
        foreach ($list as $k => $v) {
            $url = GetHttpType() . $_SERVER['HTTP_HOST'] . '/img/' . $v['img_id'];
            $list[$k]['url'] = $url;
            if ($v['uid'] > 0) {
                $list[$k]['username'] = User::GetUserById($v['uid'])['username'];
            }
            if ($v['uid'] == 0) {
                $list[$k]['username'] = '游客';
            }
            if ($v['uid'] == -1) {
                $list[$k]['username'] = '管理员';
            }
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

    public static function get_img_ip($id)
    {
        return self::where(['img_id' => $id])->value('ip');
    }

    public static function del_img($id)
    {
        $info = self::where([
            'img_id' => $id
        ])->find();
        if (strpos($info['url'], 'cdn.jsdelivr.net') !== false) {
            $github_config = [
                'USER' => config('USER'),
                'REPO' => config('REPO'),
                'MAIL' => config('MAIL'),
                'TOKEN' => config('TOKEN')
            ];
            Github::del_file($info['img_name'], $info['sha'], $github_config);
        }
        return self::where(['img_id' => $id])->delete();
    }

    public static function get_statistical()
    {
        $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $yesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        //今日上传文件数
        $today_uploads = self::where('addtime', '>=', $today)->count();
        //今日上传大小
        $today_upload_size = GetFileSize(self::where('addtime', '>=', $today)->sum('size'));
        //昨日上传文件数
        $yesterday_uploads = self::where('addtime', '>=', $yesterday)
            ->where('addtime', '<', $today)
            ->count();
        //昨日上传大小
        $yesterday_upload_size = GetFileSize(self::where('addtime', '>=', $yesterday)
            ->where('addtime', '<', $today)
            ->sum('size'));
        //总上传文件数
        $uploads = self::count();
        //总上传大小
        $upload_size = GetFileSize(self::sum('size'));
        return [
            'today_uploads' => $today_uploads,
            'today_upload_size' => $today_upload_size,
            'yesterday_uploads' => $yesterday_uploads,
            'yesterday_upload_size' => $yesterday_upload_size,
            'uploads' => $uploads,
            'upload_size' => $upload_size
        ];
    }

    public static function get_one_hour_uploads()
    {
        return self::where(
            'addtime',
            '>=',
            time() - 3600
        )->count();
    }
}