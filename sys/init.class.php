<?php

namespace sys;

class Init {

	private static $route, $session, $load, $util, $view, $request;

	function __construct()
	{
		self::$route = new \sys\Route;
		self::$session = new \sys\Session;
		self::$load = new \sys\Load;

		self::$load->controller(self::$route->controller())
			->dispatch(\sys\Route::method__, self::$route->page(), self::$route->action(), self::$route->params());
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

	public static function util()
	{
		return (isset(self::$util)) ? self::$util : self::$util = new \sys\Util;
	}

	public static function view()
	{
		return (isset(self::$view)) ? self::$view : self::$view = new \sys\View;
	}

	public static function request()
	{
		return (isset(self::$request)) ? self::$request : self::$request = new \sys\modules\Request;
	}

}
