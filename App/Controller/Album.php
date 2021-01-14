<?php

namespace App\Controller;

use QAQ\Kernel\Jump;

class Album extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!config('album')) return Jump::info('探索已关闭！');
        $info = [
            'nav' => config('navs')
        ];
        return view('home/album', $info);
    }
}