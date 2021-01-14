<?php
/*
 * 用户中心路由
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/7/30
 */

use QAQ\Kernel\Route;

//用户中心路由组
Route::group('/user', function () {
    //首页
    Route::get('/', 'User/index');

    //统计
    Route::post('/get_statistical', 'User/get_statistical');

    //图片列表
    Route::get('/img_list', 'User/img_list');
    Route::post('/get_img_list/^', 'User/get_img_list');

    //删除图片
    Route::post('/del_img/^', 'User/del_img');

    //Token
    Route::get('/my_token', 'User/my_token');

    //修改密码
    Route::get('/edit_pass', 'User/edit_pass');
    Route::post('/do_edit_pass', 'User/do_edit_pass');

    //退出登录
    Route::post('/logout', 'User/logout');

    //用户登录
    Route::get('/login', 'SignIn/login');
    Route::post('/do_login', 'SignIn/do_login');

    //用户注册
    Route::get('/reg', 'SignIn/reg');
    Route::post('/do_reg', 'SignIn/do_reg');

    //找回密码
    Route::get('/findpwd', 'SignIn/findpwd');
    Route::rule('/do_findpwd/^', 'SignIn/do_findpwd');
});


//验证
Route::post('/geetest', 'Geetest/index');