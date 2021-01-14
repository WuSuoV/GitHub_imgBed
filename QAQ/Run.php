<?php
/*
 * QAQ自动加载
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

use QAQ\Kernel\Config;
use QAQ\Kernel\Cache;
use QAQ\Kernel\Db;

class QAQ
{
    public static function register()
    {
        //QAQ版本
        define('QAQ_VERSION', '1.5');
        //QAQ开始运行时间
        define('QAQ_START_TIME', microtime(true));
        //设置时区
        date_default_timezone_set('Asia/Shanghai');
        //系统目录
        define('QAQ_CORE_DIR', str_replace('Public', '', dirname($_SERVER['SCRIPT_FILENAME'])));
        //检测php版本
        if (version_compare(PHP_VERSION, '7.2.0', '<')) {
            throw new \Exception('QAQ require phpversion > 7.2.0！');
        }
        //QAQ核心函数
        include __DIR__ . DIRECTORY_SEPARATOR . 'QAQ_FUNCTION.php';
        //引入第三方插件
        include __DIR__ . DIRECTORY_SEPARATOR . 'Vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        //引入第三方助手函数
        include __DIR__ . DIRECTORY_SEPARATOR . 'Vendor' . DIRECTORY_SEPARATOR . 'topthink' . DIRECTORY_SEPARATOR . 'think-helper' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'helper.php';
        //自动注册
        spl_autoload_register([
            new self,
            'autoload'
        ]);
        //QAQ错误处理注册
        self::registerError();
        //QAQ初始化
        self::init();
    }

    public static function init()
    {
        //应用目录
        $App_Dir = self::DirMap('App');
        //配置文件
        if (is_file($App_Dir . DIRECTORY_SEPARATOR . 'Config.php')) {
            define('CONFIG', include $App_Dir . DIRECTORY_SEPARATOR . 'Config.php');
            foreach (CONFIG as $_k => $_v) {
                Config::set($_k, $_v);
            }
        } else {
            throw new \Exception('QAQ Config File No Exists！');
        }
        //全局公共文件
        if (is_file($App_Dir . DIRECTORY_SEPARATOR . 'Function.php')) {
            include $App_Dir . DIRECTORY_SEPARATOR . 'Function.php';
        } else {
            throw new \Exception('QAQ Function File No Exists！');
        }
        //Db
        if (Config::get('database')) {
            $db_config = Config::get('database');
            Db::setConfig($db_config);
            if (!DEBUG) {
                if (!Cache::get('db_check')) {
                    try {
                        Db::query('show tables');
                        Cache::set('db_check', 1);
                    } catch (\Exception $e) {
                        throw new \Exception('QAQ Db Error！');
                    }
                }
            }
        }
    }

    public static function registerError()
    {
        //设定错误和异常处理
        register_shutdown_function('QAQ\Kernel\Error::fatalError');
        set_error_handler('QAQ\Kernel\Error::appError');
        set_exception_handler('QAQ\Kernel\Error::appException');
    }

    public static function autoload($class)
    {
        $file = self::findFile($class);
        if (file_exists($file)) {
            self::includeFile($file);
        }
    }

    private static function findFile($class)
    {
        $Class = substr($class, 0, strpos($class, '\\'));
        $vendorDir = self::DirMap($Class);
        $filePath = substr($class, strlen($Class)) . '.php';
        return strtr($vendorDir . $filePath, '\\', DIRECTORY_SEPARATOR);
    }

    private static function includeFile($file)
    {
        include $file;
    }

    public static function DirMap($Class)
    {
        return QAQ_CORE_DIR . $Class;
    }
}