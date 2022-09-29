<?php

namespace Phplite\Http;

class Response
{

    public static function json($data)
    {
        return json_encode($data);
    }
    public static function output($data)
    {
        if (!$data) {
            return;
        }
        if (!is_string($data)) {
            $data =  static::json($data);
        }

        echo $data;
    }
}