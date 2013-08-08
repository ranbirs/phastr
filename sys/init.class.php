<?php

namespace sys;

use sys\Route;
use sys\Load;
use sys\View;

class Init {

	private static $route, $load, $view, $session, $request;

	function __construct()
	{
		self::$route = new Route();
		self::$load = new Load();
		self::$view = new View();

		if (isset(self::$route->error)) {
			self::$view->error(404, self::$route->error);
		}
		self::$load->conf('autoload');
		self::$load->controller(self::$route->controller())
			->dispatch(self::$route->defaults['method'], self::$route->page(), self::$route->action(), self::$route->args());

		exit();
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

	public static function session($new = false)
	{
		if (!isset(self::$session) or $new) {
			self::$load->conf('hash');
			self::$session = new \sys\Session();
		}
		return self::$session;
	}

	public static function request()
	{
		if (!isset(self::$request)) {
			self::$load->conf('request');
			self::$request = new \sys\modules\Request();
		}
		return self::$request;
	}

}
