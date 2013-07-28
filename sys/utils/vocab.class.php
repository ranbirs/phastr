<?php

namespace sys\utils;

class Vocab {

	public static function t($const)
	{
		return constant("\\app\\vocabs\\" . $const . "__");
	}

}
