<?php
/*
 * QAQ Session引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class Session
{
    private static function start($lifeTime = false)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            if ($lifeTime) {
                session_set_cookie_params($lifeTime);
            }
            session_start();
            self::CheckSession();
        }
    }

    public static function set($key, $value, $ttl = false)
    {
        self::start($ttl);
        $session['data'] = $value;
        if ($ttl) {
            if (!is_int($ttl)) {
                throw new \Exception('The ttl must to be int');
            }
            if ($ttl < 1) {
                throw new \Exception('The ttl must be greater than 0');
            }
            $session['expire'] = time() + $ttl;
        }
        return $_SESSION[$key] = $session;
    }

    public static function get($key)
    {
        self::start();
        if (isset($_SESSION[$key])) {
            if (isset($_SESSION[$key]['expire'])) {
                if ($_SESSION[$key]['expire'] >= time()) {
                    return $_SESSION[$key]['data'];
                }
                self::Clear($key);
            }
            return $_SESSION[$key]['data'];
        }
        return false;
    }

    public static function Clear($key)
    {
        self::start();
        unset($_SESSION[$key]);
        if (!isset($_SESSION[$key])) return true;
        return false;
    }

    public static function ClearAll()
    {
        self::start();
        $_SESSION = [];
        if (Cookie::get(session_name())) {
            Cookie::del(session_name());
        }
        session_destroy();
        if (count($_SESSION) < 1) return true;
        return false;
    }

    public static function CheckSession()
    {
        foreach ($_SESSION as $k => $v) {
            if (isset($v['expire'])) {
                if ($v['expire'] < time()) {
                    unset($_SESSION[$k]);
                }
            }
        }
    }
}