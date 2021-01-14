<?php

namespace App\Service;

use App\Model\Img;
use App\Model\User;

class Upload
{
    public static function do_upload($file, $uid)
    {
        //临时文件名
        $tmp_name = $file['fileupload']['tmp_name'];
        //文件大小
        $tmp_size = $file['fileupload']['size'];
        //文件原名称
        $tmp_file_name = $file['fileupload']['name'];
        //允许上传的文件类型
        $allowtype = explode(',', config('allowtype'));
        //检查上传文件类型
        $tmp_file_name_arr = explode(".", $tmp_file_name);
        $file_type = array_pop($tmp_file_name_arr);
        if (!in_array($file_type, $allowtype)) {
            @unlink($tmp_name);
            return [
                'success' => false,
                'error' => '文件类型不是图片或被管理员禁止！'
            ];
        }
        //文件信息
        try {
            $img_tmp_info = getimagesize($tmp_name);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => '获取图片信息失败！'
            ];
        }
        //检查文件大小
        if ($tmp_size > config('max_upload')) {
            @unlink($tmp_name);
            return [
                'success' => false,
                'error' => '您上传的图片超过' . round(config('max_uplaod') / 1024 / 1024, 0) . 'MB了哦！'
            ];
        }
        //检查用户配额是否用完
        if ($uid > 0) {
            $uinfo = User::GetUserById($uid);
            //计算已用配额
            $size = Img::where([
                'uid' => $uid
            ])->sum('size');
            if (($size + $tmp_size) > ($uinfo['user_pre'] * 1024 * 1024 * 1024)) return [
                'success' => false,
                'error' => '您的可用空间配额不足！'
            ];
        }
        //检查文件是否完全上传
        if (is_uploaded_file($tmp_name)) {
            $img_name = md5(time() . $tmp_name) . '.' . $file_type;
            //开始上传
            $res = self::upload_file($img_name, $tmp_name);
            if (!$res) return [
                'success' => false,
                'error' => '哎呀！上传失败了，请重试！'
            ];
            $row = Img::insertGetID([
                'url' => $res['url'],
                'img_name' => $res['url'],
                'ip' => real_ip(),
                'sha' => $res['sha'],
                'width' => $img_tmp_info[0],
                'height' => $img_tmp_info[1],
                'mime' => $img_tmp_info['mime'],
                'size' => $tmp_size,
                'uid' => $uid,
                'addtime' => time()
            ]);
            if (!$row) return [
                'success' => false,
                'error' => '哎呀！上传失败了，请重试！'
            ];
            Github::do_async_upload($row);
            $url = GetHttpType() . $_SERVER['HTTP_HOST'] . '/img/' . $row;
            return [
                'success' => true,
                'url' => $url
            ];
        } else {
            return [
                'success' => false,
                'error' => '哎呀！上传失败了，请重试！'
            ];
        }
    }

    public static function upload_file($img_name, $tmp_name)
    {
        if (!is_dir('./Upload')) {
            try {
                mkdir('./Upload', 0777);
            } catch (\Exception $e) {
                return false;
            }
        }
        $file_dir = './Upload/' . $img_name;
        try {
            if (file_put_contents($file_dir, file_get_contents($tmp_name))) {
                @unlink($tmp_name);
                return [
                    'url' => $img_name,
                    'sha' => time()
                ];
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}