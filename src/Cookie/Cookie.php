<?php

namespace Phplite\Cookie;

class Cookie
{
    //set Cookie and return it's value
    public static function set($key, $value)
    {
        $expired = time() + (1 * 365 * 24 * 60 * 60);
        setcookie($key, $value, $expired, '/', '', false, true);
        return $value;
    }

    public static function has($key)
    {
        return isset($_COOKIE[$key]);
    }

    //get value using key
    public static function get($key)
    {
        return static::has($key) ? $_COOKIE[$key] : null;
    }

    //remove Cookie by key
    public static function  remove($key)
    {
        unset($_COOKIE[$key]);
        setcookie($key, null, '-1', '/');
    }

    //all Cookie
    public static function all()
    {
        return $_COOKIE;
    }

    //destroy  all Cookies
    public  static function destroy()
    {
        foreach (static::all() as $key => $value) {
            static::remove($key);
        }
    }
}