<?php

namespace sys\utils;

class Loader {

	public static function includeFile($path, $ext = ".php")
	{
		require_once \sys\utils\Helper::getPath($path) . $ext;
	}

	public static function resolveFile($path, $base = app__, $ext = ".php")
	{
		$path = \sys\utils\Helper::getPath(\sys\base($path, $base)) . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	public static function resolveInclude($path, $subj, $new = true, $base = app__, $ext = ".php")
	{
		self::includeFile(\sys\base($subj . "s/". $path, $base), $ext);
		return ($new) ? self::resolveInstance($path, $subj, $base) : true;
	}

	public static function resolveInstance($path, $subj, $base = app__)
	{
		$path = \sys\utils\Helper::getPath($subj . "s/" . $path);
		$class = \sys\utils\Helper::getPathClass("\\$base\\" . $path);
		return new $class;
	}

}
