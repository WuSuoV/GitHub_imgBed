<?php
/*
 * 前台路由
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/7/30
 */

use QAQ\Kernel\Route;

//图片
Route::get('/img/^', 'Api/img');

//首页
Route::get('/index', 'Index/index');
Route::get('/', 'Index/index');

//探索
Route::get('/album', 'Album/index');
