<?php

namespace sys\components;

class Compositor {

	private static $instance;

	function __construct()
	{
		self::$instance = &$this;
	}

	final public static function instance()
	{
		return self::$instance;
	}

}
