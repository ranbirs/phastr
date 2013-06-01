<?php

namespace sys\utils;

class Vocab {

	private static $lang;

	public static function t($term, $lang = null, $suffix = "__")
	{
		return constant("\\app\\vocabs\\$term" . $suffix);
	}

}
