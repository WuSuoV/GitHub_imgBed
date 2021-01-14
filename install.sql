CREATE TABLE IF NOT EXISTS `img_black_ip` (
  `id` int(10) unsigned NOT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `addtime` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `img_config` (
  `k` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `v` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `img_config` (`k`, `v`) VALUES
('ADMIN_DIR', 'admin'),
('admin_img', 'https://cdn.jsdelivr.net/gh/YanYuHanYun/cdn/c93be513329ada026df744f50a9fbce4.jpg'),
('album', '1'),
('allowtype', 'jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG,webp'),
('api_sw', '1'),
('copy', '烟雨寒云'),
('description', '烟雨寒云旗下极简高速外链图床，轻简好用，完全免费！'),
('footer', '本站由最可爱的QAQ_CORE强力驱动'),
('gg', '111111'),
('github_api', '2'),
('keywords', '烟雨图床,Github图床,烟雨,烟雨寒云'),
('MAIL', 'admin@yyhy.me'),
('max_upload', '10485760'),
('max_uploads', '10'),
('nav', '<a href=\"https://www.yyhy.me/\" target=\"__blank\"><span class=\"icon-directions\"></span> 博客</a>|<a href=\"https://yf.yyhy.me/\" target=\"__blank\"><span class=\"icon-wallet\"></span> 赞助</a>|<a href=\"https://www.yyhy.me/178.html\" target=\"__blank\"><span class=\"icon-cloud-download\"></span> 程序下载</a>'),
('one_hour_uploads', '100'),
('password', '123456'),
('REPO', 'image'),
('sex_verify_black_ip', '1'),
('sex_verify_config', '0'),
('sex_verify_level', '2'),
('sex_verify_sw', '1'),
('sex_verify_token', '9f59bf16af6d70fe0ca87a72b73eb1ac'),
('sitename', '烟雨图床'),
('title', '极简高速外链图床'),
('TOKEN', '123456'),
('USER', 'YanYuHanYun'),
('user_pre', '50'),
('user_status_with_img', '0'),
('username', 'admin'),
('visit_upload', '1'),
('user_reg_sw', '1'),
('user_gg', '欢迎使用');
CREATE TABLE IF NOT EXISTS `img_imgs` (
  `img_id` int(10) unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `img_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `sha` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `height` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `width` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `mime` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `size` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `sex_verify` INT(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '鉴黄状态',
  `uid` INT(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '用户ID',
  `addtime` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `img_users` (
 `uid` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
 `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '用户名',
 `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '密码',
 `qq` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT 'QQ',
 `user_pre` int(10) NOT NULL DEFAULT '0' COMMENT '用户配额',
 `token` varchar(32) NOT NULL DEFAULT '0' COMMENT 'Token',
 `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
 `status` int(1) NOT NULL DEFAULT '1' COMMENT '用户状态',
 PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `img_black_ip` ADD PRIMARY KEY (`id`);
ALTER TABLE `img_config` ADD PRIMARY KEY (`k`);
ALTER TABLE `img_imgs` ADD PRIMARY KEY (`img_id`);
ALTER TABLE `img_black_ip` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `img_imgs` MODIFY `img_id` int(10) unsigned NOT NULL AUTO_INCREMENT;