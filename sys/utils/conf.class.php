<?php

namespace sys\utils;

class Conf {

	public static function k($const)
	{
		return constant("\\app\\confs\\" . $const . "__");
	}

}
