<?php

namespace sys;

class Init
{

	public static $route, $view;

	function __construct()
	{
		self::$route = new \sys\Route();
		self::$view = new \sys\View();
		
		$controller = self::$route->controller(true);
		$controller = new $controller();
		
		$controller->init(self::$route->action(true), self::$route->params());

		self::$route->error(404);

		exit();
	}

}
