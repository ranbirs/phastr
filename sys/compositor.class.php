<?php

namespace sys;

abstract class Compositor {

	protected static $instance;

	function __construct()
	{
		self::$instance = &$this;
	}

	public static function instance()
	{
		return self::$instance;
	}

}
