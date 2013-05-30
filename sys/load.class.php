<?php

namespace sys;

use sys\utils\Helper;

class Load {

	private static $base = array('sys' => \sys\sys_base__, 'app' => \sys\app_base__);

	function __construct()
	{

	}

	public function model($path)
	{
		return self::_load("models/$path", 'composite');
	}

	public function form($path)
	{
		return self::_load("forms/$path", 'composite');
	}

	public function nav($path)
	{
		return self::_load("navs/$path", 'composite');
	}

	public function controller($path)
	{
		return self::_load("controllers/$path", 'instance');
	}

	public static function conf($path)
	{
		return self::_load("confs/$path");
	}

	public static function vocab($path, $lang = "")
	{
		if ($lang)
			$path = "$lang/$path";
		return self::_load("vocabs/$path");
	}

	public static function sys($path, $control = null, $ext = ".class.php")
	{
		return self::_load($path, $control, 'sys', $ext);
	}

	public static function app($path, $control = null, $ext = ".php")
	{
		return self::_load($path, $control, 'app', $ext);
	}

	private static function _load($path, $control = null, $base = 'app', $ext = ".php")
	{
		Helper::requireFilePath(self::$base[$base] . $path, $ext);

		if (!$control) {
			return true;
		}
		return Helper::resolveClassInstance($path, $control, $base);
	}

}
