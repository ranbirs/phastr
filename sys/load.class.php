<?php

namespace sys;

use sys\components\Loader;

class Load extends Loader {

	function __construct()
	{

	}

	public function model($path)
	{
		return self::resolveInclude($path, 'model', self::composite__);
	}

	public function form($path)
	{
		return self::resolveInclude($path, 'form', self::composite__);
	}

	public function nav($path)
	{
		return self::resolveInclude($path, 'nav', self::composite__);
	}

	public static function controller($path)
	{
		return self::resolveInclude($path, 'controller', self::instance__);
	}

	public static function conf($path)
	{
		return self::resolveInclude($path, 'conf');
	}

	public static function vocab($path, $lang = "")
	{
		return self::resolveInclude((strlen($lang) ? $lang . "/" . $path : $path), 'vocab');
	}

}
