<?php

namespace Phplite\Session;


class Session
{
    public static function start()
    {
        if (!session_id()) {
            ini_set('session.use_only_cookies', 1);   //more secure
            session_start();
        }
    }

    //set session and return it's value
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return $value;
    }

    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    //get value using key
    public static function get($key)
    {
        return static::has($key) ? $_SESSION[$key] : null;
    }

    //remove session by key
    public static function  remove($key)
    {
        unset($_SESSION[$key]);
    }

    //all session
    public static function all()
    {
        return $_SESSION;
    }

    //destroy  all sessions
    public  static function destroy()
    {
        foreach (static::all() as $key => $value) {
            static::remove($key);
        }
    }

    public static function flash($key)
    {
        $value =  null;

        if (static::has($key)) {
            $value = static::get($key);
            static::remove($key);
        }

        return $value;
    }
}