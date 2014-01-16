<?php

namespace sys;

class Init {

	private static $route, $view, $load, $session, $request;

	function __construct()
	{
		self::$route = new \sys\Route;

		if (isset(self::$route->error)) {
			self::$route->error(404, self::$route->error);
		}
		self::$view = new \sys\View;
		self::$load = new \sys\Load;
		self::$load->controller(self::$route->controller())
			->dispatch(\sys\Route::method__, self::$route->page(), self::$route->action(), self::$route->params());
	}

	public static function route()
	{
		return self::$route;
	}

	public static function view()
	{
		return self::$view;
	}

	public static function load()
	{
		return self::$load;
	}

	public static function session($new = false)
	{
		if (!isset(self::$session) || $new)
			self::$session = new \sys\Session;
		return self::$session;
	}

	public static function request()
	{
		if (!isset(self::$request))
			self::$request = new \sys\modules\Request;
		return self::$request;
	}

}
