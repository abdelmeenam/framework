<?php

namespace Phplite\Http;

class Server
{
    public static function all()
    {
        return $_SERVER;
    }

    public static function has($key)
    {
        return isset($_SERVER[$key]);
    }


    public static function get($key)
    {
        return static::has($key) ? $_SERVER[$key] : null;
    }


    public static function path_info($path)
    {
        return pathinfo($path);
    }
}