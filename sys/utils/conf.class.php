<?php

namespace sys\utils;

class Conf {

	public static function k($constant, $append = "__")
	{
		return constant("\\app\\confs\\$constant" . $append);
	}

}
