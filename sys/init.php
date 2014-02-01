<?php

namespace sys;

class Init
{

	private static $util, $route, $session, $load, $view, $request;

	function __construct()
	{
		self::$util = new \sys\Util();
		self::$route = new \sys\Route();
		
		self::$session = new \sys\Session();
		self::$load = new \sys\Load();
		self::$view = new \sys\View();
		
		self::$load->controller(self::$route->controller())->dispatch(self::$route->method(), self::$route->page(), 
			self::$route->action(), self::$route->params());
	}

	public static function util()
	{
		return self::$util;
	}

	public static function route()
	{
		return self::$route;
	}

	public static function session()
	{
		return self::$session;
	}

	public static function load()
	{
		return self::$load;
	}

	public static function view()
	{
		return self::$view;
	}

	public static function request()
	{
		/* modules to load() */
		return (isset(self::$request)) ? self::$request : self::$request = new \sys\modules\Request();
	}

}
