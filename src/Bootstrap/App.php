<?php

namespace Phplite\Bootstrap;

use LDAP\Result;
use Phplite\File\File;
use Phplite\Http\Server;
use Phplite\Http\Request;
use Phplite\Session\Session;
use Phplite\Exceptions\Whoops;
use Phplite\Http\Response;
use Phplite\Route\Route;

class App
{
    public static function run()
    {
        //Register Whoops
        Whoops::handle();

        //Session
        Session::start();

        //Handle the request
        Request::handle();

        //Require all routes directory
        File::require_directory('routes');

        //Handle route
        $data = Route::handle();

        //HandleResponse and return output
        Response::output($data);





        //----------------------------------------Session
        //Session::start();
        //Session::set('test', 'lolo');
        //echo Session::has('userd');
        //Session::remove('user');
        //echo Session::flash('test');
        ///print_r(Session::all());
        // session::destroy();
        //---------------------------------------Server
        //print_r(Server::all());
        //echo Server::has('d');
        // echo Server::get('SCRIPT_NAME');
        //print_r(Server::path_info(' http://mvc.test/'));
        //echo Server::get('REQUEST_SCHEME');
        //echo Server::get('HTTP_HOST');
        //echo  Server::get('SCRIPT_NAME');
        //---------------------------------------Request
        //Request::handle();
        //echo  Request::baseUrl();
        //echo "<br>";
        //echo  Request::url();
        // echo "<br>";
        // echo  Request::query_string();
        // echo "<br>";
        // echo  Request::full_url();
        //echo  Request::method();
        //print_r(Request::all());
        //---------------------------------------File
        //echo File::root();
        // echo File::path('rouddtes');
        // echo File::exist('routes/web.php');
        //echo "<br>";
        //File::require_file('routes/web.php');
        //  File::require_directory('routes');
        // print_r(Route::allRoutes());
        //  echo Route::handle();
    }
}