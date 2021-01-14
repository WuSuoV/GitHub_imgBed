<?php
/*
 * QAQ入口文件
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

//引入启动文件
include '../QAQ/Run.php';

//注册QAQ服务
QAQ::register();

//启动服务
QAQ\Kernel\Http::Start();
