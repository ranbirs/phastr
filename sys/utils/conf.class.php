<?php

namespace sys\utils;

class Conf {

	public static function k($const, $context = 'config', $base = app__)
	{
		$const .= "__";
		$constant = self::getConst($const, $context, $base);
		if (is_null($constant)) {
			\sys\Init::load()->conf($context, $base);
			$constant = self::getConst($const, $context, $base);
		}
		return $constant;
	}

	public static function ini($path, $sections = true)
	{
		$path = get_include_path() . "/" . \sys\path_base("confs/" . $path) . ".ini";
		return @parse_ini_file($path, $sections);
	}

	public static function getConst($const, $context, $base)
	{
		return @constant($base . "\\confs\\" . $context . "\\" . $const);
	}

}
