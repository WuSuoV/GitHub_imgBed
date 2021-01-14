<?php

namespace App\Controller;

use App\Model\BlackIp;
use App\Service\SexVerify;
use QAQ\Kernel\Cache;
use App\Model\Img;
use App\Service\Github;
use App\Service\Upload;
use QAQ\Kernel\Jump;
use QAQ\Kernel\Session;
use App\Model\User;

class Api extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!config('api_sw')) return Jump::info('API未开启！');
        $info = [
            'nav' => config('navs'),
            'site_url' => GetHttpType() . $_SERVER['HTTP_HOST']
        ];
        return view('/home/api', $info);
    }

    public function upload($token = false)
    {
        if (!$token) {
            //游客
            $uid = 0;
            if (!parent::verify_user_login() && !parent::verify_admin_login() && !config('visit_upload')) return [
                'success' => false,
                'error' => '已关闭游客上传！'
            ];
            //用户
            if (parent::verify_user_login()) {
                $uid = Session::get('user_auth')['uid'];
                $uinfo = User::GetUserById($uid);
                if (!parent::verify_admin_login()) {
                    if ($uinfo['status'] != 1) return [
                        'success' => false,
                        'error' => '您的账户已被封禁，无法上传！'
                    ];
                }
            }
            //管理员
            if (parent::verify_admin_login()) {
                $uid = -1;
            }
        } else {
            //用户通过Token上传
            $uinfo = User::GetUserByToken($token);
            if (!$uinfo) return [
                'code' => -1,
                'msg' => 'Token错误！'
            ];
            if ($uinfo['status'] != 1) return [
                'code' => -1,
                'msg' => '您的账户已被封禁，无法上传！'
            ];
            $uid = $uinfo['uid'];
        }
        if (empty(config('max_upload')) || empty(config('max_uploads')) || empty(config('allowtype')) || empty(config('one_hour_uploads')) || empty(config('USER')) || empty(config('MAIL')) || empty(config('REPO')) || empty(config('TOKEN')) || empty(config('github_api'))) return [
            'success' => false,
            'error' => '憨憨站长没有配置完整信息，无法上传！'
        ];
        if (BlackIp::GetBlackIpByIp(real_ip())) return [
            'success' => false,
            'error' => '您处在本站黑名单，无法使用上传功能！'
        ];
        if (Img::get_one_hour_uploads() >= config('one_hour_uploads')) return [
            'success' => false,
            'error' => '您上传的貌似有些频繁，请等会再来试试吧！'
        ];
        if (!$_FILES) {
            return [
                'success' => false,
                'error' => '请选择文件后再操作！'
            ];
        }
        $res = Upload::do_upload($_FILES, $uid);
        return $res;
    }

    public function output($page = 1)
    {
        if (!config('album')) return [
            'code' => -1,
            'msg' => '探索已关闭！'
        ];
        $list = Img::home_img_list($page);
        return $list;
    }

    public function upload_config()
    {
        return [
            'code' => 1,
            'msg' => '拉取成功！',
            'allowtype' => explode(',', config('allowtype')),
            'max_upload' => round(config('max_upload') / 1024, 0),
            'max_uploads' => config('max_uploads')
        ];
    }

    public function img($id = false)
    {
        //兼容V1版本数据
        if (!$id) {
            $id = request()->get('id');
            if (!$id) return [
                'code' => -1,
                'msg' => '图片获取失败！'
            ];
        }
        if (Cache::get('img_' . $id)) {
            $url = Cache::get('img_' . $id);
        } else {
            $img_info = Img::where(['img_id' => $id])->find();
            if (!$img_info) return [
                'code' => -1,
                'msg' => '图片获取失败！'
            ];
            //用户封禁后禁止访问
            if (config('user_status_with_img')) {
                if ($img_info['uid'] > 0) {
                    $uinfo = User::GetUserById($img_info['uid']);
                    if ($uinfo) {
                        if ($uinfo['status'] != 1) return [
                            'code' => -1,
                            'msg' => '该用户已被封禁，图片禁止访问！'
                        ];
                    }
                }
            }
            if (strpos($img_info['url'], 'cdn.jsdelivr.net') !== false) {
                $url = $img_info['url'];
            } else {
                Github::do_async_upload($id);
                $url = GetHttpType() . $_SERVER['HTTP_HOST'] . '/Upload/' . $img_info['url'];
                return redirect($url);
            }
            //黄图
            if ($img_info['sex_verify'] == 2) return [
                'code' => -1,
                'msg' => '图片已违规！'
            ];
            Cache::set('img_' . $id, $url);
        }
        return redirect($url);
    }

    public function async_upload($id, $syskey)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if (Cache::get('Upload_' . $id)) return [
            'code' => -1,
            'msg' => '任务已在队列中！'
        ];
        Cache::set('Upload_' . $id, 1, 60);
        if ($syskey != GetSyskey()) return [
            'code' => -1,
            'msg' => '系统秘钥有误！'
        ];
        $img_info = Img::where(['img_id' => $id])->find();
        if (!$img_info) return [
            'code' => -1,
            'msg' => '图片获取失败！'
        ];
        $file_dir = './Upload/' . $img_info['url'];
        if (!$img_info) {
            if (is_file($file_dir)) {
                @unlink($file_dir);
            }
        } else {
            if (strpos($img_info['url'], 'cdn.jsdelivr.net') !== false) {
                if (is_file($file_dir)) {
                    @unlink($file_dir);
                }
            } else {
                $github_config = [
                    'USER' => config('USER'),
                    'REPO' => config('REPO'),
                    'MAIL' => config('MAIL'),
                    'TOKEN' => config('TOKEN')
                ];
                $res = Github::upload_file_to_github($img_info['url'], $file_dir, $github_config);
                if ($res) {
                    Img::where(['img_id' => $id])->update([
                        'url' => $res['url'],
                        'sha' => $res['sha']
                    ]);
                    $file_dir = './Upload/' . $img_info['img_name'];
                    if (is_file($file_dir)) {
                        @unlink($file_dir);
                    }
                } else {
                    sleep(mt_rand(5, 30));
                    Github::do_async_upload($id);
                }
            }
        }
        Cache::clear('Upload_' . $id);
    }

    public function verify_file()
    {
        Github::do_async_verify_file();
        return [
            'code' => 1,
            'msg' => '提交文件检查成功！'
        ];
    }

    public function async_verify_file($syskey)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($syskey != GetSyskey()) return [
            'code' => -1,
            'msg' => '系统秘钥有误！'
        ];
        $files = glob('./Upload/*');
        if (count($files) > 0) {
            foreach ($files as $file) {
                $file = explode('/', $file)[2];
                $img_info = Img::where(['img_name' => $file])->find();
                $file_dir = './Upload/' . $file;
                if (!$img_info) {
                    if (is_file($file_dir)) {
                        @unlink($file_dir);
                    }
                } else {
                    if (strpos($img_info['url'], 'cdn.jsdelivr.net') !== false) {
                        if (is_file($file_dir)) {
                            @unlink($file_dir);
                        }
                    } else {
                        Github::do_async_upload($img_info['img_id']);
                    }
                }
            }
        }
    }

    public function sex_verify()
    {
        return SexVerify::SubmitAWork();
    }

    public function async_sex_verify($syskey)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if ($syskey != GetSyskey()) return [
            'code' => -1,
            'msg' => '系统秘钥有误！'
        ];
        SexVerify::DoVerify();
    }
}