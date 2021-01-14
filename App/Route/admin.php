<?php
/*
 * 后台路由
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/7/30
 */

use App\Model\Config;
use QAQ\Kernel\Route;

//加载站点信息
Config::site_info();
//定义后台路径
define('ADMIN_DIR', config('ADMIN_DIR'));

//后台路由组
Route::group('/' . ADMIN_DIR, function () {
    //退出登录
    Route::post('/logout', 'Admin/logout');

    //API延迟检测
    Route::post('/do_api_check', 'Admin/do_api_check');

    //Github配置检测
    Route::post('/do_github_check', 'Admin/do_github_check');

    //获取统计信息
    Route::post('/get_statistical', 'Admin/get_statistical');

    //移除黑名单
    Route::post('/del_black/^', 'Admin/del_black');

    //黑名单列表
    Route::get('/black_list', 'Admin/black_list');
    Route::post('/get_black_list/^', 'Admin/get_black_list');

    //系统信息
    Route::get('/system_info', 'Admin/system_info');

    //检查更新
    Route::post('/update/^', 'Admin/update');

    //删除并加入黑名单
    Route::post('/del_img_and_black_ip/^', 'Admin/del_img_and_black_ip');

    //删除图片
    Route::post('/del_img/^', 'Admin/del_img');

    //加入黑名单
    Route::post('/black_ip/^', 'Admin/black_ip');

    //图片列表
    Route::get('/img_list', 'Admin/img_list');
    Route::post('/get_img_list/^', 'Admin/get_img_list');

    //清除缓存
    Route::post('/clear_cache', 'Admin/clear_cache');

    //配置
    Route::get('/config', 'Admin/config');
    Route::get('/site_config', 'Admin/site_config');
    Route::get('/github_config', 'Admin/github_config');
    Route::get('/upload_config', 'Admin/upload_config');
    Route::get('/admin_config', 'Admin/admin_config');
    Route::get('/sex_config', 'Admin/sex_config');
    Route::get('/user_config', 'Admin/user_config');
    Route::post('/do_config', 'Admin/do_config');

    //用户列表
    Route::get('/user', 'Admin/user');
    Route::post('/get_user_list/^', 'Admin/get_user_list');

    //用户状态
    Route::post('/user_status/^', 'Admin/user_status');

    //用户配额调整
    Route::post('/user_pre/^', 'Admin/user_pre');

    //登录
    Route::get('/login', 'Login/index');
    Route::post('/do_login', 'Login/do_login');

    //首页
    Route::get('/index', 'Admin/Index');
    Route::get('/', 'Admin/Index');
});