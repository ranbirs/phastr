<?php

namespace sys\utils;

class Vocab {

	public static function t($const, $context, $lang = \app\confs\config\lang__)
	{
		$constant = self::_get($const, $context);
		if (is_null($constant)) {
			\sys\Init::load()->conf($context, $lang);
			$constant = self::_get($const, $context);
		}
		return $constant;
	}

	private static function _get($const, $context)
	{
		return @constant("\\app\\vocabs\\" . $context . "\\" . $const);
	}

}
