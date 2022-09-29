<?php

namespace Phplite\Url;

use Phplite\Http\Request;
use Phplite\Http\Server;

class Url
{
    //users/1/edit  -> https://phplite.com/users/1/edit
    public static function path($path)
    {
        return Request::baseUrl() . '/' . trim($path, '/');
    }

    public static function previous()
    {
        return Server::get('HTTP_REFERER');
    }

    public static function redirect($path)
    {
        header('LOCATION: ' . $path);
        exit();
    }
}