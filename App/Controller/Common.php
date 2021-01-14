<?php

namespace App\Controller;

use App\Model\Config;
use App\Model\User;
use QAQ\Kernel\App;
use QAQ\Kernel\Session;

class Common extends App
{
    public function __construct()
    {
        //检查根目录是否有更新包
        if (is_file(QAQ_CORE_DIR . 'update.zip')) {
            @unlink(QAQ_CORE_DIR . 'update.zip');
        }
        //返回登录标识
        if ($this->verify_user_login()) {
            define('USER_IS_LOGIN', true);
        } else {
            define('USER_IS_LOGIN', false);
        }
        if ($this->verify_admin_login()) {
            define('ADMIN_IS_LOGIN', true);
        } else {
            define('ADMIN_IS_LOGIN', false);
        }
    }

    public function verify_admin_login()
    {
        $session = Session::get('admin_auth');
        if (!$session || !$session['username'] || !$session['password'] || $session['username'] != config('username') || $session['password'] != config('password')) return false;
        return true;
    }

    public function verify_user_login()
    {
        if (Session::get('user_auth')) {
            $uinfo = Session::get('user_auth');
            $uinfo = User::GetUserById($uinfo['uid']);
            if (!$uinfo) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
}