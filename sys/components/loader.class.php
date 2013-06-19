<?php

namespace sys\components;

use sys\utils\Helper;

class Loader {

	public static function includeFile($path, $ext = ".php")
	{
		require_once Helper::getPath($path) . $ext;
	}

	public static function resolveFile($path, $base = 'app', $ext = ".php")
	{
		$path = Helper::getPath($base . "/" . $path) . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : $file;
	}

	protected static function resolveInclude($path, $control = null, $base = 'app', $ext = ".php")
	{
		self::includeFile($base . "/" . $path, $ext);

		if (!$control) {
			return true;
		}
		return self::resolveInstance($path, $control, $base);
	}

	private static function resolveInstance($path, $control = null, $base = 'app')
	{
		$path = Helper::getPath($path);
		$class = "\\$base\\" . Helper::getPathClass($path);
		$instance = new $class;

		switch ($control) {
			case 'composite':
				$prop = Helper::getPathName($path);
				$comp = \sys\components\Compositor::instance();
				$comp->$prop = $instance;
				break;
		}
		return $instance;
	}

}
