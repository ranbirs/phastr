<?php

namespace sys;

class Init
{

	private static $route, $view;

	function __construct()
	{
		self::$route = new \sys\Route();
		self::$view = new \sys\View();
		
		$controller = self::$route->controller(true);
		$controller = new $controller();
		
		if (!$controller->init(self::$route->action(true), self::$route->params())) {
			self::$route->error(404);
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
