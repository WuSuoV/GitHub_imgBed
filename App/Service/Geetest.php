<?php

namespace App\Service;

use QAQ\Kernel\Session;

class Geetest
{
    public static function GetVerify()
    {
        $Sdk = new \App\Plug\Geetest();
        $data = [
            'user_id' => GetUserVisitId(),
            'client_type' => 'web',
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];
        $status = $Sdk->pre_process($data, 1);
        Session::set('gtserver', $status);
        Session::set('user_id', GetUserVisitId());
        return $Sdk->get_response_str();
    }

    public static function DoVerify($data)
    {
        $GtSdk = new \App\Plug\Geetest();
        $Gdata = [
            'user_id' => GetUserVisitId(),
            'client_type' => 'web',
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];
        if (Session::get('gtserver') == 1) {
            $result = $GtSdk->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $Gdata);
            if ($result) {
                $geetest = true;
            } else {
                $geetest = false;
            }
        } else {
            if ($GtSdk->fail_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'])) {
                $geetest = true;
            } else {
                $geetest = false;
            }
        }
        if (!$geetest) return false;
        return true;
    }
}