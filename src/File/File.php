<?php

namespace Phplite\File;

class File
{
    public static function root()
    {
        return ROOT;
    }

    public static function ds()
    {
        return DS;
    }

    //get file all path
    public static function path($path)
    {
        $path = static::root() . static::ds() . trim($path, '/');
        $path = str_replace(['/', '\\'], static::ds(), $path);
        return $path;
    }

    //check file exists
    public static function exist($path)
    {
        return file_exists(static::path($path));
    }

    //require a file
    public static function require_file($path)
    {
        if (static::exist($path)) {
            return require_once static::path($path);
        }
    }

    //include a file
    public static function include_file($path)
    {
        if (static::exist($path)) {
            return include static::path($path);
        }
    }

    //require all files of specific directory
    public static function require_directory($path)
    {
        if (static::exist($path)) {
            $files = array_diff(scandir(static::path($path)), ['.', '..']);
            foreach ($files as $file) {
                $file_path = $path . static::ds() . $file;
                static::require_file($file_path);
            }
        } else {
            //Invalid Routes name;
            throw new \Exception("Invalid Routes name", 1);
        }
    }
}