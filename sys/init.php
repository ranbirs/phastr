<?php

namespace sys;

class Init
{

	private static $route, $load, $view;

	function __construct()
	{
		self::$route = new \sys\Route();
		
		self::$load = new \sys\Load();
		self::$view = new \sys\View();
		
		self::$load->controller(self::$route->controller())->dispatch(self::$route->method(), self::$route->page(), 
			self::$route->action(), self::$route->params());
	}

	public static function route()
	{
		return self::$route;
	}

	public static function load()
	{
		return self::$load;
	}

	public static function view()
	{
		return self::$view;
	}

}
