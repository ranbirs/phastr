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
		$path = Helper::getPath(\sys\base($path, $base)) . "." . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	public static function resolveInclude($path, $subj, $new = true, $base = app__, $ext = "php")
	{
		self::includeFile(\sys\base($subj . "s/". $path, $base), $ext);
		return ($new) ? self::getInstance($path, $subj, $base) : true;
	}

	public static function getInstance($path, $subj, $base = app__)
	{
		$path = Helper::getPath($subj . "s/" . $path);
		$class = Helper::getPathClass("\\$base\\" . $path);
		return new $class;
	}

}
