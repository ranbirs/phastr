<?php

namespace sys\utils;

use sys\Init;

class Path
{
	
	public static $route;
	
    public static function route($key = null, $path = null)
    {
        return Init::$init->route->route($key) . ((isset($path)) ? '/' . $path : ''); // @todo rm Init ref
    }

    public static function label($path = null)
    {
        return preg_replace('/[^a-z0-9_]/i', '_', $path);
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

    public static function action($path = null)
    {
        return self::route('label')[0] . '/' . ((isset($path)) ? $path : self::route('label')[1]);
    }

    public static function uri($path = null, $rewrite = false)
    {
        return (($rewrite) ? self::route('base') : self::route('file')) . (($path = trim($path, '/')) ? '/' . $path : '');
    }

}
