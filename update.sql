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
INSERT INTO `img_config` (`k`, `v`) VALUES ('ADMIN_DIR', 'admin');
INSERT INTO `img_config` (`k`, `v`) VALUES ('admin_img', 'https://cdn.jsdelivr.net/gh/YanYuHanYun/cdn/c93be513329ada026df744f50a9fbce4.jpg');
INSERT INTO `img_config` (`k`, `v`) VALUES ('album', '1');
INSERT INTO `img_config` (`k`, `v`) VALUES ('allowtype', 'jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG,webp');
INSERT INTO `img_config` (`k`, `v`) VALUES ('api_sw', '1');
INSERT INTO `img_config` (`k`, `v`) VALUES ('copy', '烟雨寒云');
INSERT INTO `img_config` (`k`, `v`) VALUES ('description', '烟雨寒云旗下极简高速外链图床，轻简好用，完全免费！');
INSERT INTO `img_config` (`k`, `v`) VALUES ('gg', '111111');
INSERT INTO `img_config` (`k`, `v`) VALUES ('github_api', '2');
INSERT INTO `img_config` (`k`, `v`) VALUES ('keywords', '烟雨图床,Github图床,烟雨,烟雨寒云');
INSERT INTO `img_config` (`k`, `v`) VALUES ('MAIL', 'admin@yyhy.me');
INSERT INTO `img_config` (`k`, `v`) VALUES ('max_upload', '10485760');
INSERT INTO `img_config` (`k`, `v`) VALUES ('max_uploads', '10');
INSERT INTO `img_config` (`k`, `v`) VALUES ('nav', '<a href=\"https://www.yyhy.me/\" target=\"__blank\"><span class=\"icon-directions\"></span> 博客</a>|<a href=\"https://yf.yyhy.me/\" target=\"__blank\"><span class=\"icon-wallet\"></span> 赞助</a>|<a href=\"https://www.yyhy.me/178.html\" target=\"__blank\"><span class=\"icon-cloud-download\"></span> 程序下载</a>');
INSERT INTO `img_config` (`k`, `v`) VALUES ('one_hour_uploads', '100');
INSERT INTO `img_config` (`k`, `v`) VALUES ('password', '123456');
INSERT INTO `img_config` (`k`, `v`) VALUES ('REPO', 'image');
INSERT INTO `img_config` (`k`, `v`) VALUES ('sex_verify_black_ip', '1');
INSERT INTO `img_config` (`k`, `v`) VALUES ('sex_verify_config', '0');
INSERT INTO `img_config` (`k`, `v`) VALUES ('sex_verify_level', '2');
INSERT INTO `img_config` (`k`, `v`) VALUES ('sex_verify_sw', '1');
INSERT INTO `img_config` (`k`, `v`) VALUES ('sex_verify_sw', '1');
INSERT INTO `img_config` (`k`, `v`) VALUES ('sex_verify_token', '9f59bf16af6d70fe0ca87a72b73eb1ac');
INSERT INTO `img_config` (`k`, `v`) VALUES ('sitename', '烟雨图床');
INSERT INTO `img_config` (`k`, `v`) VALUES ('title', '极简高速外链图床');
INSERT INTO `img_config` (`k`, `v`) VALUES ('TOKEN', '123456');
INSERT INTO `img_config` (`k`, `v`) VALUES ('USER', 'YanYuHanYun');
INSERT INTO `img_config` (`k`, `v`) VALUES ('user_pre', '50');
INSERT INTO `img_config` (`k`, `v`) VALUES ('user_status_with_img', '0');
INSERT INTO `img_config` (`k`, `v`) VALUES ('username', 'admin');
INSERT INTO `img_config` (`k`, `v`) VALUES ('visit_upload', '1');
INSERT INTO `img_config` (`k`, `v`) VALUES ('user_gg', '欢迎使用');
INSERT INTO `img_config` (`k`, `v`) VALUES ('user_reg_sw', '1');
ALTER TABLE `img_imgs` ADD `sex_verify` INT(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '鉴黄状态' AFTER `size`;
ALTER TABLE `img_imgs` ADD `uid` INT(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '用户ID' AFTER `sex_verify`;
ALTER TABLE `img_users` ADD `token` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT 'Token' AFTER `user_pre`;