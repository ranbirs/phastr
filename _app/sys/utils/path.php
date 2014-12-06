<?php

namespace sys\utils;

use sys\Init;
use app\configs\Route as __route;

class Path
{

    public static function route($key = null)
    {
        return Init::$init->route->path($key);
    }

    public static function label($path = null)
    {
        return str_replace('-', '_', $path);
    }

    public static function resolve($file)
    {
        return (($file = stream_resolve_include_path($file)) !== false) ? $file : false;
    }

    public static function root($path = null)
    {
        return (isset($path)) ? $_SERVER['DOCUMENT_ROOT'] . '/' . $path : $_SERVER['DOCUMENT_ROOT'];
    }

    public static function base($path = null)
    {
        return rtrim(self::route('base'), '/') . '/' . $path;
    }

    public static function page($path = null)
    {
        return self::route('label')[0] . '/' . ((isset($path)) ? $path : self::route('label')[1]);
    }

    public static function uri($path = null)
    {
        $uri = (__route::rewrite__) ? self::route('base') : self::route('file');
        return $uri .= ($path = trim($path, '/')) ? '/' . $path : '';
    }

    public static function request($path = null)
    {
        return self::uri(self::route('route') . '/' . __route::request__ . '/' . $path);
    }

    public static function trail($path = null)
    {
        return ($path) ? $path . __route::trail__ : '';
    }

}
