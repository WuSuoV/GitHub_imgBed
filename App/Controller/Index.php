<?php

namespace App\Controller;

use App\Model\Img;

class Index extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $count = Img::count();
        $info = [
            'count' => $count,
            'nav' => config('navs')
        ];
        return view('home/index', $info);
    }
}