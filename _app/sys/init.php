<?php

namespace sys;

abstract class Init
{

	public static $init;

	public $route;

	function __construct(Route &$route)
	{
		self::$init = &$this;
		
		$this->route = $route;
	}

}