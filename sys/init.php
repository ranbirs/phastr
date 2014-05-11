<?php

namespace sys;

class Init
{

	private static $route, $view;

	function __construct($route, $view)
	{
		self::$route = $route;
		self::$view = $view;
		
		$controller = $route->controller(true);
		
		(new $controller())->dispatch($route);
	}

	public static function route()
	{
		return self::$route;
	}

	public static function view()
	{
		return self::$view;
	}

}
