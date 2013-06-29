<?php

namespace sys;

use sys\components\Loader;

class Load extends Loader {

	function __construct()
	{

	}

	public function model($path)
	{
		return self::resolveInclude($path, 'model', 'composite');
	}

	public function form($path)
	{
		return self::resolveInclude($path, 'form', 'composite');
	}

	public function nav($path)
	{
		return self::resolveInclude($path, 'nav', 'composite');
	}

	public static function controller($path)
	{
		return self::resolveInclude($path, 'controller', 'instance');
	}

	public static function conf($path)
	{
		return self::resolveInclude($path, 'conf');
	}

	public static function vocab($path, $lang = "")
	{
		if ($lang)
			$path = $lang . "/" . $path;
		return self::resolveInclude($path, 'vocab');
	}

}
