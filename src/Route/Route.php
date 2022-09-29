<?php

namespace Phplite\Route;

use Phplite\Http\Request;
use Phplite\View\View;

//get-post-any-prefix-middleware
class Route
{
    //all routes that written by developer
    private static $routes = [];
    private static $middleware;
    private static $prefix;

    //Add method
    private static function add($methods, $uri, $callback)
    {
        $uri = rtrim(static::$prefix . '/' . trim($uri, '/'), '/');
        $uri = $uri ?: '/';
        foreach (explode('|', $methods) as $method) {
            static::$routes[] = [
                'uri'       => $uri,
                'callback'  => $callback,
                'method'    => $method,
                'middleware' => static::$middleware
            ];
        }
    }

    public static  function get($uri, $callback)
    {
        static::add('GET', $uri, $callback);
    }

    public static function post($uri, $callback)
    {
        static::add('POST', $uri, $callback);
    }

    public static function any($uri, $callback)
    {
        static::add('GET|POST', $uri, $callback);
    }

    public static function allRoutes()
    {
        return static::$routes;
    }

    public static function prefix($prefix, $callback)
    {
        $parent_prefix = static::$prefix;
        static::$prefix .= '/' . trim($prefix, '/');
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \Exception("please provide valid callback function");
        }
        static::$prefix = $parent_prefix;
    }

    public static function middleware($middleware, $callback)
    {
        $parenrt_middleware = static::$middleware;
        static::$middleware .= '|' . trim($middleware, '|');
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \Exception("please provide valid callback function");
        }
        static::$middleware = $parenrt_middleware;
    }

    //Handel the request 
    public static function handle()
    {
        $uri = Request::url();     //user write this

        foreach (static::$routes as $route) {
            $matched = true;
            $route['uri'] = preg_replace('/\/{.*?}/', '/(.*?)', $route['uri']);   //routes u have in web.php
            $route['uri'] = '#^' . $route['uri'] . '$#';

            if (preg_match($route['uri'], $uri, $matches)) {
                array_shift($matches);
                $params = array_values($matches);

                foreach ($params as $param) {
                    if (strpos($param, '/')) {
                        $matched = false;
                    }
                }

                if ($route['method'] != Request::method()) {
                    $matched = false;
                }

                if ($matched == true) {
                    return static::invoke($route, $params);
                }
            }
        }
        return View::render('errors.404');
    }

    public static function invoke($route, $params = [0])
    {
        static::executeMiddleware($route);          //middleware first
        $callback = $route['callback'];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        } elseif (strpos($callback, '@') !== false) {
            list($controller, $method) = explode('@', $callback);
            $controller = 'App\Controllers\\' . $controller;

            if (class_exists($controller)) {
                $object = new $controller;  //object

                if (method_exists($object, $method)) {
                    return call_user_func_array([$object, $method], $params);
                } else {
                    throw new \BadFunctionCallException("The method " . $method . " isn't exists at " . $controller);
                }
            } else {
                throw new \ReflectionException("class " . $controller . " isn't found");
            }
        } else {
            throw new \InvalidArgumentException("Please provide a valid callback function");
        }
    }

    public static function executeMiddleware($route)
    {
        foreach (explode('|', $route['middleware']) as $middleware) {
            if ($middleware != '') {
                $middleware = 'App\Midlewares\\' . $middleware;
                if (class_exists($middleware)) {
                    $object = new $middleware;  //object
                    call_user_func_array([$object, 'handle'], []);
                } else {
                    throw new \ReflectionException("Middleware " . $middleware . " isn't found");
                }
            }
        }
    }
}