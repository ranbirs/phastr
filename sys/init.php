<?php

namespace sys;

class Init
{

	private static $route, $view;

	function __construct()
	{
		self::$route = new \sys\Route();
		self::$view = new \sys\View();
		
		$controller = '\\' . app__ . '\\controllers\\' . self::$route->controller();
		(new $controller())->dispatch(self::$route->methods(), self::$route->page(), self::$route->action(), 
			self::$route->params());
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
