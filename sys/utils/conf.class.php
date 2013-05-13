<?php

namespace sys\utils;

class Conf {

	public static function k($constant, $suffix = "__")
	{
		return constant("\\app\\confs\\$constant" . $suffix);
	}

}
