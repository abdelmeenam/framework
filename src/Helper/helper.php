<?php
// Render view file
if (!function_exists('view')) {
    function view($path, $data = [])
    {
        return Phplite\View\View::render($path, $data);
    }
}

if (!function_exists('request')) {
    function request($key)
    {
        return Phplite\Http\Request::value($key);
    }
}

if (!function_exists('redirect')) {
    function redirect($path)
    {
        return Phplite\Url\Url::redirect($path);
    }
}

if (!function_exists('previous')) {
    function previous()
    {
        return Phplite\Url\Url::previous();
    }
}

if (!function_exists('url')) {
    function url($path)
    {
        return Phplite\Url\Url::path($path);
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        return Phplite\Url\Url::path($path);
    }
}

if (!function_exists('dd')) {
    function dd($data)
    {
        echo "<pre>";
        if (is_string($data)) {
            echo $data;
        } else {
            print_r($data);
        }
        echo "<pre>";
        die();
    }
}

if (!function_exists('session')) {
    function session($key)
    {
        return Phplite\Session\Session::get($key);
    }
}

if (!function_exists('flash')) {
    function flash($key)
    {
        return Phplite\Session\Session::flash($key);
    }
}

if (!function_exists('links')) {
    function links($currentpage, $pages)
    {
        return Phplite\Database\Database::links($currentpage, $pages);
    }
}

//Table Auth
if (!function_exists('auth')) {
    function auth($table)
    {
        $auth =  Phplite\Session\Session::get($table) ?: phplite\Cookie\Cookie::get($table);
        return  Phplite\Database\Database::table($table)->where('id', '=', $auth)->first();
    }
}