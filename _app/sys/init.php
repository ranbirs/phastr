<?php

namespace sys;

class Init
{

	public static $route, $view, $init;

	function __construct()
	{
		self::$route = new \sys\Route();
		self::$view = new \sys\View();
		
		$controller = self::$route->controller(true);
		self::$init = new $controller();
		
		self::$init->init(self::$route->action(true), self::$route->params());

		self::$route->error(404);

		exit();
	}

}
