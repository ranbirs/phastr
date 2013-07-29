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

	public static function load()
	{
		if (!isset(self::$load))
			self::$load = new \sys\Load();
		return self::$load;
	}

	public static function view()
	{
		if (!isset(self::$view))
			self::$view = new \sys\View();
		return self::$view;
	}

	public static function session()
	{
		if (!isset(self::$session)) {
			Load::conf('hash');
			self::$session = new \sys\Session();
		}
		return self::$session;
	}

	public static function request()
	{
		if (!isset(self::$request)) {
			Load::conf('request');
			self::$request = new \sys\components\Request();
		}
		return self::$request;
	}

}
