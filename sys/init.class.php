<?php

namespace sys;

use sys\Route;
use sys\View;
use sys\Load;

class Init {

	private static $route, $view, $load, $session, $request, $rest;

	function __construct()
	{
		self::$route = new Route();
		self::$view = new View();

		if (isset(self::$route->error)) {
			self::$view->error(404, self::$route->error);
		}
		self::$load = new Load();
		self::$load->conf('autoload');
		self::$load->controller(self::$route->controller())
			->dispatch(Route::method__, self::$route->page(), self::$route->action(), self::$route->params());
	}

	public static function route()
	{
		return self::$route;
	}

	public static function view($new = false)
	{
		return (!$new) ? self::$view : new View();
	}

	public static function load()
	{
		return self::$load;
	}

	public static function session($new = false)
	{
		if (!isset(self::$session) or $new)
			self::$session = new \sys\Session();
		return self::$session;
	}

	public static function request()
	{
		if (!isset(self::$request))
			self::$request = new \sys\modules\Request();
		return self::$request;
	}

	public static function rest($new = false)
	{
		if (!isset(self::$rest) or $new)
			self::$rest = new \sys\modules\Rest();
		return self::$rest;
	}

}
