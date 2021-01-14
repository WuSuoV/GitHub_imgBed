<?php

namespace App\Controller;

use App\Model\Img;
use QAQ\Kernel\Jump;
use QAQ\Kernel\Session;
use App\Model\User as UserModel;

class User extends Common
{
    public function __construct()
    {
        parent::__construct();
        if (!parent::verify_user_login()) {
            $this->no_login();
        }
        //封禁检查
        $this->verify_status();
    }

    public function no_login()
    {
        return Jump::error('您还未登录！', '/user/login');
    }

    public function verify_status()
    {
        $uinfo = UserModel::GetUserById(Session::get('user_auth')['uid']);
        if ($uinfo['status'] != 1) {
            Session::Clear('user_auth');
            return Jump::error('账户已被封停，请联系管理员处理！');
        }
    }

    public function index()
    {
        $info = [
            'title' => '用户中心',
            'uinfo' => Session::get('user_auth'),
            'imgs' => Img::GetCountByUid(Session::get('user_auth')['uid']),
            'used_pre' => Img::GetPreByUid(Session::get('user_auth')['uid']),
        ];
        return view('user/index', $info);
    }

    public function get_statistical()
    {
        return Img::get_statistical();
    }

    public function img_list()
    {
        $info = [
            'title' => '图片列表'
        ];
        return view('user/img_list', $info);
    }

    public function get_img_list($page = 1)
    {
        return Img::user_img_list(Session::get('user_auth')['uid'], $page);
    }

    public function del_img($id)
    {
        if (Img::DelImgByIdWithUid(Session::get('user_auth')['uid'], $id)) return [
            'code' => 1,
            'msg' => '删除成功！'
        ];
        return [
            'code' => -1,
            'msg' => '删除失败！'
        ];
    }

    public function my_token()
    {
        if (!Session::get('user_auth')['token'] || Session::get('user_auth')['token'] != md5(Session::get('user_auth')['password'])) {
            UserModel::UpdateTokenById(Session::get('user_auth')['uid'], md5(Session::get('user_auth')['password']));
        }
        $info = [
            'title' => 'Token',
            'token' => md5(Session::get('user_auth')['password'])
        ];
        return view('user/token', $info);
    }

    public function edit_pass()
    {
        $info = [
            'title' => '修改密码'
        ];
        return view('user/edit_pass', $info);
    }

    public function do_edit_pass()
    {
        $old_password = request()->post('old_password');
        $new_password = request()->post('new_password');
        $re_password = request()->post('re_password');
        if (!$old_password) return [
            'code' => -1,
            'msg' => '旧密码不可为空！'
        ];
        if (!$new_password) return [
            'code' => -1,
            'msg' => '新密码不可为空！'
        ];
        if (!$re_password) return [
            'code' => -1,
            'msg' => '请再输入一次新密码！'
        ];
        if (!password_verify($old_password, Session::get('user_auth')['password'])) return [
            'code' => -1,
            'msg' => '旧密码有误！'
        ];
        if ($new_password != $re_password) return [
            'code' => -1,
            'msg' => '两次新密码输入不一致！'
        ];
        if (UserModel::UpdatePassByUid(Session::get('user_auth')['uid'], $new_password)) {
            Session::Clear('user_auth');
            return [
                'code' => 1,
                'msg' => '重置密码成功，请重新登录！'
            ];
        }
        return [
            'code' => -1,
            'msg' => '修改密码失败，请稍后重试！'
        ];
    }

    public function logout()
    {
        Session::Clear('user_auth');
        return [
            'code' => 1,
            'msg' => '退出登录成功！'
        ];
    }
}