<?php

namespace sys;

use sys\Controller;

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

	public function sys($path, $control = null, $ext = ".class.php")
	{
		return self::_load($path, $control, 'sys', $ext);
	}

	public function app($path, $control = null, $ext = ".php")
	{
		return self::_load($path, $control, 'app', $ext);
	}

	public static function conf($path)
	{
		return self::_load("confs/$path");
	}

	public static function vocab($path, $lang = true)
	{
		if ($lang)
			$path = Res::session()->client('lang') . "/$path";

		return self::_load("vocabs/$path");
	}

	private static function _load($path, $control = null, $base = 'app', $ext = ".php")
	{
		$path = Helper::getPath($path);
		require_once self::$base[$base] . $path . $ext;

		if (!$control) {
			return true;
		}

		$class = "\\$base\\" . Helper::getPathClass($path);

		if ($control == 'instance') {
			return new $class();
		}

		if ($control == 'composite') {
			$prop = Helper::getFileName($path);
			$inst = Controller::instance();
			$inst->$prop = new $class();
		}

		return true;
	}

}
