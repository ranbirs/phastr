<?php

namespace sys\utils;

class Vocab {

	public static function t($const, $context, $lang = \app\confs\config\lang__)
	{
		$constant = self::getConst($const, $context);
		if (is_null($constant)) {
			\sys\Init::load()->conf($context, $lang);
			$constant = self::getConst($const, $context);
		}
		return $constant;
	}

	public static function getConst($const, $context)
	{
		return constant('\\app\\vocabs\\' . $context . '\\' . $const);
	}

}
