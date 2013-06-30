<?php

namespace sys\utils;

class Vocab {

	private static $lang;

	public static function t($const, $lang = null, $append = "__")
	{
		return constant("\\app\\vocabs\\" . $const . $append);
	}

}
