<?php

namespace sys;

class Init
{

	public static $route, $view, $init;

	function __construct()
	{
		self::$route = new \sys\Route();
		self::$init = new self::$route->path['class']();

		if (method_exists(self::$init, self::$route->path['method'])) {
		    self::$view = new \sys\View();
		    self::$init->init(self::$route->path['label'][1], self::$route->path['label'][2], self::$route->path['params']);
		    self::$init->{self::$route->path['method']}(self::$route->path['params']);
		    self::$init->render();
		}
		self::$route->error(404);

		exit();
	}

}
