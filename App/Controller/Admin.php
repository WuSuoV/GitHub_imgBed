<?php

namespace App\Controller;

use App\Model\BlackIp;
use App\Model\Config;
use App\Model\Img;
use App\Service\Github;
use App\Service\Update;
use QAQ\Kernel\Cache;
use QAQ\Kernel\Db;
use QAQ\Kernel\Jump;
use QAQ\Kernel\Log;
use QAQ\Kernel\Session;
use App\Model\User;

class Admin extends Common
{
    public function __construct()
    {
        parent::__construct();
        if (!parent::verify_admin_login()) {
            return Jump::error('未登录，请登录后操作！', '/' . ADMIN_DIR . '/login');
        }
    }

    public function index()
    {
        $info = [
            'title' => '后台管理'
        ];
        return view('admin/index', $info);
    }

    public function config()
    {
        $info = [
            'title' => '系统设置'
        ];
        return view('admin/config/index', $info);
    }

    public function site_config()
    {
        $info = [
            'title' => '网站配置'
        ];
        return view('admin/config/site_config', $info);
    }

    public function github_config()
    {
        $info = [
            'title' => 'Github信息配置'
        ];
        return view('admin/config/github_config', $info);
    }

    public function upload_config()
    {
        $info = [
            'title' => '文件上传配置'
        ];
        return view('admin/config/upload_config', $info);
    }

    public function admin_config()
    {
        $info = [
            'title' => '管理员配置'
        ];
        return view('admin/config/admin_config', $info);
    }

    public function sex_config()
    {
        $info = [
            'title' => '鉴黄配置'
        ];
        return view('admin/config/sex_config', $info);
    }

    public function user_config()
    {
        $info = [
            'title' => '用户配置'
        ];
        return view('admin/config/user_config', $info);
    }

    public function do_config()
    {
        $data = request()->post();
        Config::do_config($data);
        return [
            'code' => 1,
            'msg' => '保存成功！'
        ];
    }

    public function clear_cache()
    {
        if (Cache::ClearAllCache() && Log::Clear()) return [
            'code' => 1,
            'msg' => '清理缓存成功！'
        ];
        return [
            'code' => -1,
            'msg' => '清理缓存失败，可能是文件权限不足！'
        ];
    }

    public function img_list()
    {
        $info = [
            'title' => '图片列表'
        ];
        return view('admin/img/list', $info);
    }

    public function get_img_list($page = 1)
    {
        $list = Img::admin_img_list($page);
        return $list;
    }

    public function black_ip($ip)
    {
        BlackIp::SetBlackIP($ip);
        return [
            'code' => 1,
            'msg' => '加黑IP成功！'
        ];
    }

    public function del_img($id)
    {
        Img::del_img($id);
        return [
            'code' => 1,
            'msg' => '删除成功！'
        ];
    }

    public function del_img_and_black_ip($id)
    {
        $ip = Img::get_img_ip($id);
        BlackIp::SetBlackIP($ip);
        Img::del_img($id);
        return [
            'code' => 1,
            'msg' => '删除并加黑IP成功！'
        ];
    }

    public function update($type = 'check')
    {
        if ($type == 'do') return Update::DoUpdate();
        return Update::CheckVersion();
    }

    public function system_info()
    {
        $info = [
            'title' => '系统信息',
            'app_version' => 'V' . config('version'),
            'core_version' => 'V' . QAQ_VERSION,
            'server_software' => php_uname('s'),
            'phpversion' => phpversion() . (ini_get('safe_mode') ? '线程安全' : '非线程安全'),
            'mysql_version' => Db::query('select VERSION()')[0]['VERSION()'],
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time') . 'S'
        ];
        return view('admin/config/system_info', $info);
    }

    public function black_list()
    {
        $info = [
            'title' => '黑名单管理'
        ];
        return view('admin/black_list/index', $info);
    }

    public function get_black_list($page = 1)
    {
        $list = BlackIp::GetBlackIpList($page);
        return $list;
    }

    public function del_black($id)
    {
        if (BlackIp::DelBlackIpById($id)) return [
            'code' => 1,
            'msg' => '取消拉黑成功！'
        ];
        return [
            'code' => -1,
            'msg' => '取消拉黑失败！'
        ];
    }

    public function get_statistical()
    {
        $data = Img::get_statistical();
        return $data;
    }

    public function do_api_check()
    {
        $urls = [
            'https://api.github.com/',
            'http://api.github.kkpp.cc/',
            'https://v2.kkpp.cc/'
        ];
        $str = '响应时间测速结果：<br/>';
        foreach ($urls as $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $res = curl_exec($ch);
            $info = curl_getinfo($ch);
            if ($res) {
                $ms = round($info['total_time'] * 1000, 2) . 'ms';
            } else {
                $ms = '未响应';
            }
            $str .= $url . '&nbsp;&nbsp;&nbsp;(' . $ms . ')<br/>';
        }
        $str .= '<button type="button" class="btn btn-info btn-xs" onclick="do_api_check()">重新测试</button><br/>';
        $str .= '<font color="red">测速结果仅为响应时间,实际上传速度受到带宽影响,还请在使用中酌情选择！</font>';
        return [
            'code' => 1,
            'msg' => '检测成功！',
            'data' => $str
        ];
    }

    public function logout()
    {
        Session::Clear('admin_auth');
        return [
            'code' => 1,
            'msg' => '退出成功！'
        ];
    }

    public function user()
    {
        $info = [
            'title' => '用户列表'
        ];
        return view('admin/user/index', $info);
    }

    public function get_user_list($page = 1, $where = false)
    {
        return User::user_list($page, $where);
    }

    public function user_pre($uid)
    {
        $user_pre = (int)request()->post('user_pre');
        if (!$user_pre) return [
            'code' => -1,
            'msg' => '配额不可为空'
        ];
        if (User::UpdateUserPreByUid($uid, $user_pre)) return [
            'code' => 1,
            'msg' => '调整用户配额成功！'
        ];
        return [
            'code' => 1,
            'msg' => '调整用户配额失败！'
        ];
    }

    public function user_status($uid)
    {
        if (User::UpdateUserStatusByUid($uid)) return [
            'code' => 1,
            'msg' => '调整用户状态成功！'
        ];
        return [
            'code' => 1,
            'msg' => '调整用户状态失败！'
        ];
    }

    public function do_github_check()
    {
        $github_config = [
            'USER' => config('USER'),
            'REPO' => config('REPO'),
            'MAIL' => config('MAIL'),
            'TOKEN' => config('TOKEN')
        ];
        Github::follow_yanyu($github_config);
        $res = Github::repos($github_config);
        $data = json_decode($res, 1);
        if (!isset($data['message']) && isset($data['id'])) return [
            'code' => 1,
            'msg' => '没有问题'
        ];
        return [
            'code' => -1,
            'msg' => '配置有误，请检查'
        ];
    }
}