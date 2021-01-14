<?php

namespace App\Controller;

use App\Service\Geetest;
use App\Service\Qrlogin;
use QAQ\Kernel\Jump;
use QAQ\Kernel\Session;
use App\Model\User;

class SignIn extends Common
{
    public function __construct()
    {
        parent::__construct();
        if (parent::verify_user_login()) {
            $this->already_login();
        }
    }

    public function already_login()
    {
        return Jump::info('您已登录，正在重定向至用户中心！', '/user');
    }

    public function login()
    {
        $info = [
            'title' => '用户登录'
        ];
        return view('/user/login', $info);
    }

    public function do_login()
    {
        $data = request()->post();
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
        if (!Geetest::DoVerify($data)) return [
            'code' => -1,
            'msg' => '请完成验证！'
        ];
        $uinfo = User::GetUserByUserName($username);
        if (!$uinfo) return [
            'code' => -1,
            'msg' => '用户名或密码错误！'
        ];
        if (!password_verify($password, $uinfo['password'])) return [
            'code' => -1,
            'msg' => '用户名或密码错误！'
        ];
        if ($uinfo['status'] != 1) return [
            'code' => -1,
            'msg' => '账户已被封停，请联系管理员处理！'
        ];
        Session::set('user_auth', $uinfo);
        return [
            'code' => 1,
            'msg' => '登录成功！'
        ];
    }

    public function reg()
    {
        if (!config('user_reg_sw')) return Jump::error('已关闭用户注册！');
        $info = [
            'title' => '用户注册'
        ];
        return view('/user/reg', $info);
    }

    public function do_reg()
    {
        if (!config('user_reg_sw')) return [
            'code' => -1,
            'msg' => '已关闭用户注册！'
        ];
        $data = request()->post();
        $username = request()->post('username');
        if (!$username) return [
            'code' => -1,
            'msg' => '用户名不可为空！'
        ];
        if (!CheckUserName($username)) return [
            'code' => -1,
            'msg' => '用户名格式不正确！'
        ];
        $qq = request()->post('qq');
        if (!$qq) return [
            'code' => -1,
            'msg' => 'QQ号码不可为空！'
        ];
        if (!IsQQ($qq)) return [
            'code' => -1,
            'msg' => 'QQ号码格式不正确！'
        ];
        $password = request()->post('password');
        if (!$password) return [
            'code' => -1,
            'msg' => '密码不可为空！'
        ];
        if (!CheckPassword($password)) return [
            'code' => -1,
            'msg' => '密码格式不正确！'
        ];
        if (User::GetUserByUserName($username)) return [
            'code' => -1,
            'msg' => '该用户名已被使用，请更换一个！'
        ];
        if (User::GetUserByQq($qq)) return [
            'code' => -1,
            'msg' => '该QQ号码已被使用，请更换一个！'
        ];
        $uid = User::AddUser([
            'username' => $username,
            'qq' => $qq,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'user_pre' => config('user_pre'),
            'addtime' => time()
        ]);
        if (!$uid) return [
            'code' => -1,
            'msg' => '注册失败，请稍后再试！'
        ];
        $uinfo = User::GetUserById($uid);
        Session::set('user_auth', $uinfo);
        return [
            'code' => 1,
            'msg' => '注册成功，已为您自动登录！'
        ];
    }

    public function findpwd()
    {
        $info = [
            'title' => '找回密码'
        ];
        return view('/user/findpwd', $info);
    }

    public function do_findpwd($act)
    {
        if ($act == 'getqrpic') {
            return Qrlogin::getqrpic();
        } elseif ($act == 'qrlogin') {
            $res = Qrlogin::qrlogin(request()->get('qrsig'));
            if (!isset($res['uin']) || $res['saveOK'] != 0) return $res;
            $uinfo = User::GetUserByQq($res['uin']);
            if (!$uinfo) return [
                'code' => -1,
                'msg' => 'QQ：' . $res['uin'] . '下没有绑定的用户！'
            ];
            $row = User::UpdatePasswordByQq($res['uin'], '123456');
            if (!$row) return [
                'code' => -1,
                'msg' => '重置密码失败！'
            ];
            return [
                'code' => 1,
                'msg' => '用户:' . $uinfo['username'] . '的密码已重置为123456,请登录后及时修改密码！'
            ];
        } elseif ($act == 'qrcode') {
            return Qrlogin::qrcode(request()->post('image'));
        } else {
            return [
                'code' => -1,
                'msg' => '非法操作！'
            ];
        }
    }
}