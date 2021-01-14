<?php
/*
 * API路由
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/7/30
 */

use QAQ\Kernel\Route;

//API路由组
Route::group('/api', function () {
    //旧版图片接口
    Route::get('/img/^', 'Api/img');

    //API首页
    Route::get('/', 'Api/index');

    //上传
    Route::post('/upload/^', 'Api/upload');

    //探索
    Route::get('/output/^', 'Api/output');

    //拉取配置
    Route::get('/upload_config', 'Api/upload_config');

    //异步上传
    Route::get('/async_upload/^', 'Api/async_upload');

    //文件检查
    Route::get('/verify_file', 'Api/verify_file');
    Route::get('/async_verify_file/^', 'Api/async_verify_file');

    //鉴黄
    Route::get('/sex_verify', 'Api/sex_verify');
    Route::get('/async_sex_verify/^', 'Api/async_sex_verify');
});
