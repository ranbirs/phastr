<?php

namespace sys;

class Init
{
	
	use \sys\traits\Load;

	private static $route, $view;

	function __construct()
	{
		self::$route = new \sys\Route();
		self::$view = new \sys\View();
		
		$this->load()->init(self::$route->controller())->dispatch(self::$route->method(), self::$route->page(), 
			self::$route->action(), self::$route->params());
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
