<?php

namespace sys;

use sys\Route;
use sys\Load;
use sys\View;

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
			->dispatch(\app\confs\route\method__, self::$route->page(), self::$route->action(), self::$route->params());
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

	public static function rest($new = false)
	{
		if (!isset(self::$rest) or $new) {
			self::$load->conf('rest');
			self::$rest = new \sys\modules\Rest();
		}
		return self::$rest;
	}

}
