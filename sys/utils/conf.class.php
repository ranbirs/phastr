<?php

namespace sys\utils;

class Conf {

	public static function k($const, $append = "__")
	{
		return constant("\\app\\confs\\" . $const . $append);
	}

}
