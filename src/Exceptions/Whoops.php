<?php

namespace Phplite\Exceptions;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class Whoops
{

    public static function handle()
    {
        $whoops = new Run();
        $whoops->pushHandler(new PrettyPageHandler());
        $whoops->register();
    }
}