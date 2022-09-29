<?php

namespace Phplite\View;

use Phplite\File\File;
use Jenssegers\Blade\Blade;
use Phplite\Session\Session;

class View
{
    public static function bladeRender($path, $data = [])
    {
        $blade = new Blade(File::path('views'), File::path('storage/cache'));

        return $blade->make($path, $data)->render();
    }

    public static function Viewrender($path, $data = [])
    {
        $path = 'views' . File::ds() . str_replace(['/', '\\', '.'],  File::ds(), $path) . '.php';

        if (!File::exist($path)) {
            throw new \Exception("The view file {$path} is not exist");
        }

        ob_start();
        //['name'=>'ali']   $name ? ali
        extract($data);
        include File::path($path);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public static function render($path, $data = [])
    {
        $errors = Session::flash('errors');
        $old = Session::flash('errors');
        $data = array_merge($data, ['errors' => $errors, 'old' => $old]);
        return static::bladeRender($path, $data);
    }
}