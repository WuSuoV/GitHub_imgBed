<?php
/*
 * QAQ 路由引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class Route
{
    protected static $group = '';
    protected static $rules = [];

    public static function register($route_files)
    {
        //初始化
        self::$rules['get'] = [];
        self::$rules['post'] = [];
        self::$rules['rule'] = [];
        //注册路由
        foreach ($route_files as $route_file) {
            if (!file_exists($route_file)) {
                throw new \Exception('QAQ Route File No Exists！');
            }
            include $route_file;
        }
        return new self();
    }

    public static function get($url, $action)
    {
        self::SetRule('get', $url, $action);
    }

    public static function post($url, $action)
    {
        self::SetRule('post', $url, $action);
    }

    public static function rule($url, $action)
    {
        self::SetRule('rule', $url, $action);
    }

    private static function SetRule($type, $url, $action)
    {
        //拼接分组注册前缀
        $url = self::$group . $url;
        //去除最后的/
        if ($url[strlen($url) - 1] == '/' && $url != '/') {
            $url = substr($url, 0, strlen($url) - 1);
        }
        self::$rules[$type][$url] = $action;
    }

    public static function group($name, $func)
    {
        //设置分组
        self::$group .= $name;
        //执行闭包
        $func();
        //分组路由注册完毕,清空此次的累加
        self::$group = explode($name, self::$group)[0];
    }

    public static function AnalyticUrl()
    {
        $path = Http::GetPath();
        //路由模式
        if (Config::get('must_route')) {
            //注册并匹配路由
            $url = self::register(Config::get('route_files'))->FindRule($path, Http::RequestType());
        }
        //开始解析
        $params = explode('/', $url);
        //去空
        $params = array_filter($params);
        //重新排列下标
        $params = array_merge($params);
        //默认控制器
        if (count($params) < 1) $params[0] = Config::get('default_controller_name');
        //默认操作
        if (count($params) < 2) $params[1] = Config::get('default_action_name');
        //多应用模式
        if (Config::get('multi_app')) {
            //默认模块
            if (count($params) < 1) $params[0] = Config::get('default_module_name');
            //默认控制器
            if (count($params) < 2) $params[1] = Config::get('default_controller_name');
            //默认操作
            if (count($params) < 3) $params[2] = Config::get('default_action_name');
            //设置基础请求参数
            $module = $params[0];
            $controller = $params[1];
            $action = $params[2];
            $i = 3;
        } else {
            //设置基础请求参数
            $controller = $params[0];
            $action = $params[1];
            $i = 2;
        }
        //设置参数
        $value = [];
        while ($i < count($params)) {
            $value[] = $params[$i];
            $i++;
        }
        $params = [
            'path' => $path,
            'params' => $params,
            'module' => $module ?? false,
            'controller' => $controller,
            'action' => $action,
            'value' => $value
        ];
        return $params;
    }

    public function FindRule($url, $type)
    {
        //去除最后的/
        if ($url[strlen($url) - 1] == '/' && $url != '/') {
            $url = substr($url, 0, strlen($url) - 1);
        }
        $rules = self::$rules[$type];
        foreach ($rules as $rule => $real) {
            //路由兼容处理
            $rule = str_replace('//', '/', $rule);
            //直接匹配到
            if ($url == $rule) return $real;
            //开始进行规则匹配
            if (strpos($rule, '/^') !== false) {
                //去除^
                $rule = str_replace('/^', '', $rule);
                //防止去除完为空
                if ($rule == '') $rule = '/';
                //再次尝试直接匹配
                if ($url == $rule) return $real;
                //正则匹配
                $preg = '~^\\' . $rule . '~';
                if (preg_match($preg, $url)) {
                    $value = str_replace_once($rule, '', $url);
                    //防止value前面没有/
                    if (isset($value[0]) && $value[0] != '/') $value = '/' . $value;
                    return $real . $value;
                }
            }
        }
        //匹配任意路由
        if ($type != 'rule') {
            return self::FindRule($url, 'rule');
        }
        //路由不存在
        throw new \Exception('QAQ Route Rule Not Found！', -2000);
    }
}