<?php

namespace sys\utils;

use sys\Init;

class Path
{

	public static $route;

	public static function route($path = null, $rewrite = false)
	{
		return self::uri(Init::$init->route->route('route', true) . ((isset($path)) ? '/' . $path : ''), $rewrite);
	}

	public static function uri($path = null, $rewrite = false)
	{
		$route = Init::$init->route->route;
		return (($rewrite) ? $route['base'] : $route['file']) . (($path = trim($path, '/')) ? '/' . $path : $route['uri']);
	}

	public static function base($path = null)
	{
		return rtrim(Init::$init->route->route['base'], '/') . '/' . $path;
	}

	public static function root($path = null)
	{
		return (isset($path)) ? $_SERVER['DOCUMENT_ROOT'] . '/' . $path : $_SERVER['DOCUMENT_ROOT'];
	}

	public static function label($path = null)
	{
		return preg_replace('/[^a-z0-9_]/i', '_', $path);
	}

	public static function resolve($file)
	{
		return (($file = stream_resolve_include_path($file)) !== false) ? $file : false;
	}

}