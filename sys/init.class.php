<?php

namespace sys;

use sys\Route;

class Init {

	private static $route, $util, $load, $view, $session, $request;

	function __construct()
	{
		self::$route = new Route;

		if (isset(self::$route->error)) {
			self::$route->error(404, self::$route->error);
		}
		self::load()->controller(self::$route->controller())
			->dispatch(Route::method__, self::$route->page(), self::$route->action(), self::$route->params());
	}

	public static function route()
	{
		return self::$route;
	}

	public static function util()
	{
		if (!isset(self::$util))
			self::$util = new \sys\Util;
		return self::$util;
	}

	public static function load()
	{
		if (!isset(self::$load))
			self::$load = new \sys\Load;
		return self::$load;
	}

	public static function view()
	{
		if (!isset(self::$view))
			self::$view = new \sys\View;
		return self::$view;
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
