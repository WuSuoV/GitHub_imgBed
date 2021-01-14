<?php
/*
 * QAQ APP引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class App extends Jump
{
    protected $path;
    protected $params;
    protected $module;
    protected $controller;
    protected $action;
    protected $class;
    protected $value;

    public function __construct()
    {
        global $params;
        $params = Route::AnalyticUrl();
        $this->path = $params['path'];
        $this->params = $params['params'];
        $this->module = $params['module'];
        $this->controller = $params['controller'];
        $this->action = $params['action'];
        $this->value = $params['value'];
    }

    public static function run()
    {
        $static = new static();
        if (Config::get('multi_app')) {
            return $static->FindModule();
        } else {
            //设置个module用于后续控制器查找
            $static->module = 'App\\Controller\\';
            return $static->FindController();
        }
    }

    public static function path()
    {
        global $params;
        return $params['path'];
    }

    public function controller()
    {
        global $params;
        return $params['controller'];
    }

    public function module()
    {
        if (!Config::get('multi_app')) return false;
        global $params;
        return $params['module'];
    }

    public function action()
    {
        global $params;
        return $params['action'];
    }

    private function FindModule()
    {
        //应用目录
        $App_Dir = \QAQ::DirMap('App');
        //寻找模块
        $module_file = $App_Dir . '/Controller/' . $this->module;
        $this->module = 'App\\Controller\\' . $this->module . '\\';
        if (!is_dir($module_file)) {
            $module_file = $App_Dir . '/Controller/' . ucfirst($this->module);
            $this->module = 'App\\Controller\\' . ucfirst($this->module) . '\\';
            if (!is_dir($module_file)) {
                throw new \Exception('QAQ Module Not Found！', -2001);
            }
        }
        //匹配控制器层
        return $this->FindController();
    }

    private function FindController()
    {
        $this->class = $this->module . $this->controller;
        if (!class_exists($this->class)) {
            $this->controller = ucfirst($this->controller);
            $this->class = $this->module . $this->controller;
            if (!class_exists($this->class)) {
                throw new \Exception('QAQ Controller Not Found！', -2002);
            }
        }
        //匹配控制器方法
        return $this->FindAction();
    }

    private function FindAction()
    {
        if (!method_exists($this->class, $this->action)) {
            $this->action = ucfirst($this->action);
            if (!method_exists($this->class, $this->action)) {
                throw new \Exception('QAQ Action Not Found！', -2003);
            }
        }
        //执行控制器代码
        $class = new $this->class;
        $action = $this->action;
        return call_user_func_array([
            $class, $action
        ], $this->value);
    }
}