<?php

namespace sys\utils;

use sys\utils\Helper;

class Loader {

	public static function includeFile($path, $ext = "php")
	{
		require_once Helper::getPath($path) . "." . $ext;
	}

	public static function resolveFile($path, $base = app__, $ext = "php")
	{
		$path = Helper::getPath(\sys\base_path($path, $base)) . "." . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	public static function resolveInclude($path, $type, $new = true, $base = app__, $ext = "php")
	{
		self::includeFile(\sys\base_path($type . "s/". $path, $base), $ext);
		return ($new) ? self::resolveInstance($path, $type, $base) : true;
	}

	public static function resolveInstance($path, $type, $base = app__)
	{
		$path = Helper::getPath($type . "s/" . $path);
		$class = Helper::getPathClass("\\$base\\" . $path);
		return new $class;
	}

}
