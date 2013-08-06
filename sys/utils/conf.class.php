<?php

namespace sys\utils;

class Conf {

	public static function k($const, $context = 'config', $base = app__)
	{
		$const .= "__";
		$constant = self::_get($const, $context, $base);
		if (is_null($constant)) {
			\sys\Load::conf($context, $base);
			$constant = self::_get($const, $context, $base);
		}
		return $constant;
	}

	private static function _get($const, $context, $base)
	{
		return @constant($base . "\\confs\\" . $context . "\\" . $const);
	}

}
