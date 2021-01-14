<?php

namespace App\Model;

use QAQ\Kernel\Cache;
use QAQ\Kernel\Model;

class Config extends Model
{
    protected $table = 'img_config';
    protected $pk = 'k';

    public static function do_config($data)
    {
        foreach ($data as $k => $v) {
            $row = self::where(['k' => $k])->find();
            if ($row) {
                self::where(['k' => $k])->update(['v' => $v]);
            } else {
                self::insert([
                    'k' => $k,
                    'v' => $v
                ]);
            }
        }
        Cache::clear('site_config');
    }

    public static function site_info()
    {
        if (!Cache::get('site_config')) {
            $configs = self::select();
            Cache::set('site_config', $configs);
            foreach ($configs as $config) {
                config($config['k'], $config['v']);
            }
        } else {
            $configs = Cache::get('site_config');
            foreach ($configs as $config) {
                config($config['k'], $config['v']);
            }
        }
        config('navs', explode('|', config('nav')));
    }
}