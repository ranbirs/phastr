<?php

namespace sys\components;

class Compositor {

	private static $instance;

	function __construct()
	{
		self::$instance = &$this;
	}

	final public static function instance($context)
	{
		self::$instance->$context = self::$instance;
		return self::$instance->$context;
	}

}
