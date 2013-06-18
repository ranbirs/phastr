<?php

namespace sys\components;

use sys\components\Loader;

class Call extends Loader {

	public static function controller($path)
	{
		return self::resolveInclude("controllers/$path", 'instance');
	}

	public static function conf($path)
	{
		return self::resolveInclude("confs/$path");
	}

	public static function vocab($path, $lang = "")
	{
		if ($lang)
			$path = "$lang/$path";
		return self::resolveInclude("vocabs/$path");
	}

	public static function sys($path, $control = null, $ext = ".class.php")
	{
		return self::resolveInclude($path, $control, 'sys', $ext);
	}

	public static function app($path, $control = null, $ext = ".php")
	{
		return self::resolveInclude($path, $control, 'app', $ext);
	}

}
