<?php

namespace App\Service;
class Github
{
    public static function do_async_upload($id)
    {
        $ip = rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);
        $UserAgent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)";
        $headers = ['X-FORWARDED-FOR:' . $ip . '', 'CLIENT-IP:' . $ip . ''];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GetHttpType() . $_SERVER['HTTP_HOST'] . '/api/async_upload/' . $id . '/' . GetSyskey());
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $UserAgent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
        return true;
    }

    public static function upload_file_to_github($img_name, $tmp_name, $github_config)
    {
        $url = get_api(config('github_api')) . 'repos/' . $github_config['USER'] . '/' . $github_config['REPO'] . '/contents/' . $img_name;
        $content = base64_encode(file_get_contents($tmp_name));
        $ch = curl_init();
        $defaultOptions = [
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode([
                'message' => 'uploadfile',
                'committer' => [
                    'name' => $github_config['USER'],
                    'email' => $github_config['MAIL'],
                ],
                'content' => $content,
            ]),
            CURLOPT_HTTPHEADER => [
                'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language:zh-CN,en-US;q=0.7,en;q=0.3',
                'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
                'Authorization:token ' . $github_config['TOKEN'],
            ],
        ];
        curl_setopt_array($ch, $defaultOptions);
        $chContents = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($chContents, true);
        if (isset($res['content']['path']) && isset($res['content']['sha']) && $res['content']['path'] != '' && $res['content']['sha'] != '') {
            $link = 'https://cdn.jsdelivr.net/gh/' . $github_config['USER'] . '/' . $github_config['REPO'] . '/' . $res['content']['path'];
            $sha = $res['content']['sha'];
            return [
                'url' => $link,
                'sha' => $sha
            ];
        } else {
            return false;
        }
    }

    public static function del_file($img_name, $sha, $github_config)
    {
        $url = get_api(config('github_api')) . 'repos/' . $github_config['USER'] . '/' . $github_config['REPO'] . '/contents/' . $img_name;
        $ch = curl_init();
        $defaultOptions = [
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => json_encode([
                'message' => 'delete a file',
                'sha' => $sha,
            ]),
            CURLOPT_HTTPHEADER => [
                'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language:zh-CN,en-US;q=0.7,en;q=0.3',
                'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
                'Authorization:token ' . $github_config['TOKEN'],
            ],
        ];
        curl_setopt_array($ch, $defaultOptions);
        $chContents = curl_exec($ch);
        curl_close($ch);
    }

    public static function repos($github_config)
    {
        $url = get_api(config('github_api')) . 'repos/' . $github_config['USER'] . '/' . $github_config['REPO'];
        $ch = curl_init();
        $defaultOptions = [
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language:zh-CN,en-US;q=0.7,en;q=0.3',
                'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
                'Authorization:token ' . $github_config['TOKEN'],
            ],
        ];
        curl_setopt_array($ch, $defaultOptions);
        $chContents = curl_exec($ch);
        curl_close($ch);
        return $chContents;
    }

    public static function follow_yanyu($github_config)
    {
        $url = get_api(config('github_api')) . 'user/following/YanYuHanYun';
        $ch = curl_init();
        $defaultOptions = [
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => [
                'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language:zh-CN,en-US;q=0.7,en;q=0.3',
                'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
                'Authorization:token ' . $github_config['TOKEN'],
            ],
        ];
        curl_setopt_array($ch, $defaultOptions);
        $chContents = curl_exec($ch);
        curl_close($ch);
        return $chContents;
    }

    public static function do_async_verify_file()
    {
        $ip = rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);
        $UserAgent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)";
        $headers = ['X-FORWARDED-FOR:' . $ip . '', 'CLIENT-IP:' . $ip . ''];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GetHttpType() . $_SERVER['HTTP_HOST'] . '/api/async_verify_file/' . GetSyskey());
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $UserAgent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
        return true;
    }
}