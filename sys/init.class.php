<?php

namespace sys;

use sys\Route;
use sys\Load;

class Init {

	private static $route, $load, $view, $session, $request;

	function __construct()
	{
		self::$route = new Route;

		if (isset(self::$route->error)) {
			self::view()->error(404, self::$route->error);
		}

		Load::conf('constants');
		Load::conf('autoload');

		if (self::$route->defaults['master']) {
			Load::controller(self::$route->defaults['master']);
		}
		Load::controller(self::$route->controller())
			->dispatch(self::$route->defaults['method'], self::$route->page(), self::$route->action(), self::$route->args());

		exit();
	}

	public static function route()
	{
		return self::$route;
	}

	public static function load($new = false)
	{
		if (!isset(self::$load) or $new)
			self::$load = new \sys\Load();
		return self::$load;
	}

	public static function view($new = false)
	{
		if (!isset(self::$view) or $new)
			self::$view = new \sys\View();
		return self::$view;
	}

	public static function session($new = false)
	{
		if (!isset(self::$session) or $new)
			self::$session = new \sys\Session();
		return self::$session;
	}

	public static function request($new = false)
	{
		if (!isset(self::$request) or $new)
			self::$request = new \sys\components\Request();
		return self::$request;
	}

}
