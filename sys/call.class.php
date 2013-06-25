<?php

namespace sys;

use sys\components\Loader;

class Call extends Loader {

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
			$path = "$lang/$path";
		return self::resolveInclude($path, 'vocab');
	}

}
