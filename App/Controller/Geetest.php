<?php

namespace App\Controller;

use App\Service\Geetest as GeetestService;

class Geetest extends Common
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return GeetestService::GetVerify();
    }
}