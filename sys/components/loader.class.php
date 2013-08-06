<?php

namespace sys\components;

use sys\utils\Helper;

class Loader {

	const instance__ = 'instance';
	const composite__ = 'composite';

	public static function includeFile($path, $ext = ".php")
	{
		require_once Helper::getPath($path) . $ext;
	}

	public static function resolveFile($path, $base = app__, $ext = ".php")
	{
		$path = Helper::getPath(\sys\base($path, $base)) . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	protected static function resolveInclude($path, $context, $control = null, $base = app__, $ext = ".php")
	{
		self::includeFile(\sys\base($context . "s/". $path, $base), $ext);

		if (is_null($control)) {
			return true;
		}
		return self::resolveInstance($path, $context, $control, $base);
	}

	private static function resolveInstance($path, $context, $control = null, $base = app__)
	{
		$path = Helper::getPath($context . "s/" . $path);
		$class = Helper::getPathClass("\\$base\\" . $path);
		$instance = new $class;

		if ($control === self::composite__) {
			$subj = Helper::getPathName($path);
			\sys\components\Compositor::instance($context)->$subj = $instance;
		}
		return $instance;
	}

}
