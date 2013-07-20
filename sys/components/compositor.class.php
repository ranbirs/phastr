<?php

namespace sys\components;

abstract class Compositor {

	private static $instance;

	function __construct()
	{
		self::$instance = &$this;
	}

	public static function instance($context)
	{
		self::$instance->$context = self::$instance;
		return self::$instance->$context;
	}

}
