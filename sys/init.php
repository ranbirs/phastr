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
		$controller = new $controller();
		
		if (!$controller->init($route->action(true), $route->params())) {
			$route->error(404);
		}
		$controller->render();
		
		exit();
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
