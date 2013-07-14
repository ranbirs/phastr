<?php

namespace sys\components;

class Singleton {

	private static $instance;

	function __construct()
	{
		if (!isset(self::$instance))
			self::$instance = new static;
	}

	public static function instance()
	{
		return self::$instance;
	}

}
