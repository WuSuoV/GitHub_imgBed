<?php

namespace App\Controller;

use QAQ\Kernel\Jump;
use QAQ\Kernel\Session;

class Login extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (parent::verify_admin_login()) return Jump::info('您已登录，正在重定向至后台首页！', '/' . ADMIN_DIR);
        $info = [
            'title' => '后台登录'
        ];
        return view('admin/login', $info);
    }

    public function do_login()
    {
        $username = request()->post('username');
        $password = request()->post('password');
        if (!$username) return [
            'code' => -1,
            'msg' => '用户名不能为空！'
        ];
        if (!$password) return [
            'code' => -1,
            'msg' => '密码不能为空！'
        ];
        if ($username != config('username') || $password != config('password')) return [
            'code' => -1,
            'msg' => '用户名或密码错误！'
        ];
        Session::set('admin_auth', [
            'username' => $username,
            'password' => $password
        ]);
        return [
            'code' => 1,
            'msg' => '登录成功！'
        ];
    }
}