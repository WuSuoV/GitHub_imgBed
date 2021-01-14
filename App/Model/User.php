<?php

namespace App\Model;

use QAQ\Kernel\Model;

class User extends Model
{
    protected $table = 'img_users';
    protected $pk = 'uid';

    public static function UpdateTokenById($uid, $token)
    {
        return self::where([
            'uid' => $uid
        ])->update([
            'token' => $token
        ]);
    }

    public static function UpdatePassByUid($uid, $password)
    {
        return self::where([
            'uid' => $uid
        ])->update([
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public static function user_list($page, $where = false)
    {
        $size = 50;
        if ($where) {
            $map1 = [
                ['username', 'like', '%' . $where . '%'],
            ];

            $map2 = [
                ['qq', 'like', '%' . $where . '%'],
            ];
            $where = [$map1, $map2];
        } else {
            $where = [];
        }
        $list = self::order('addtime', 'desc')
            ->whereOr($where)
            ->page($page, $size)
            ->select();
        if ($page != 1) {
            if (count($list) < 1) return [
                'code' => -1,
                'msg' => '没有更多了...'
            ];
        } else {
            if (count($list) < 1) return [
                'code' => 0
            ];
        }
        $count = self::whereOr($where)
            ->count();
        if ($count % $size == 0) {
            $pages = $count / $size;
        } else {
            $pages = (int)($count / $size) + 1;
        }
        return [
            'code' => 1,
            'data' => $list,
            'count' => $count,
            'page' => $page,
            'pages' => $pages
        ];
    }

    public static function UpdateUserStatusByUid($uid)
    {
        $status = self::where([
            'uid' => $uid
        ])->value('status');
        if ($status == 1) return self::where([
            'uid' => $uid
        ])->update([
            'status' => 0
        ]);
        return self::where([
            'uid' => $uid
        ])->update([
            'status' => 1
        ]);
    }

    public static function UpdateUserPreByUid($uid, $user_pre)
    {
        return self::where([
            'uid' => $uid
        ])->update([
            'user_pre' => $user_pre
        ]);
    }

    public static function GetUserByUserName($username)
    {
        return self::where([
            'username' => $username
        ])->find();
    }

    public static function GetUserByQq($qq)
    {
        return self::where([
            'qq' => $qq
        ])->find();
    }

    public static function AddUser($data)
    {
        return self::insertGetId($data);
    }

    public static function GetUserById($uid)
    {
        return self::where([
            'uid' => $uid
        ])->find();
    }

    public static function GetUserByToken($token)
    {
        return self::where([
            'token' => $token
        ])->find();
    }

    public static function UpdatePasswordByQq($qq, $password)
    {
        return self::where([
            'qq' => $qq
        ])->update([
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
}