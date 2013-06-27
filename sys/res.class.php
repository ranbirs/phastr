<?php

namespace sys;

use sys\components\Loader;
use sys\utils\Helper;

class Res {

	protected static $request, $path, $route;
	protected static $controller, $page, $action, $params = array();
	protected static $error, $defaults = array();

	protected static function init()
	{
		$qs = \app\confs\sys\query_str__;
		$request = (isset($_GET[$qs])) ? $_GET[$qs] : null;
		$request = $path = strtolower(trim($request, "/"));
		$defaults = array(
			'autoload' => \app\confs\app\autoload__,
			'homepage' => \app\confs\app\homepage__,
			'page' => \app\confs\sys\page__,
			'action' => \app\confs\sys\action__,
			'method' => \app\confs\sys\method__,
			'master' => \app\confs\sys\master__
		);

		if (!$path)
			$path = $defaults['autoload'] . "/" . $defaults['homepage'];
		$path = explode("/", $path);
		$route = array_splice($path, 0, 3);
		$params = $path;
		$path = array();

		if (!Loader::resolveFile("controllers/" . $route[0]))
			array_unshift($route, $defaults['autoload']);

		if (!isset($route[1]))
			$route[1] = $defaults['page'];
		if (!isset($route[2]))
			$route[2] = $defaults['action'];

		foreach ($route as $index => $param) {

			if ($param === $defaults['method']) {
				self::$error = \app\vocabs\sys\er_icr__;
				break;
			}
			if (preg_match('/[^a-z0-9-]/i', $param)) {
				self::$error = \app\vocabs\sys\er_icr__;
				break;
			}
			if ((strlen($param) > 128)) {
				self::$error = \app\vocabs\sys\er_icr__;
				break;
			}

			switch ($index) {
				case 0:
					$controller = $param;
					if ($controller === $defaults['master']) {
						self::$error = \app\vocabs\sys\er_icc__;
						break 2;
					}
					if ($controller !== $defaults['autoload'])
						$path[] = $controller;
					break;
				case 1:
					$page = $param;
					$path[] = $page;
					break;
				case 2:
					$action = $param;
					if ($action !== $defaults['action'])
						$path[] = $action;
					break;
			}
		}
		if (!isset(self::$error)) {
			self::$request = ($request) ? $request : "/";
			self::$path = ($request) ? implode("/", $path) : $defaults['homepage'];
			self::$route = Helper::getPath("$controller/$page/$action", 'route');
			self::$controller = Helper::getPath($controller);
			self::$page = Helper::getPath($page);
			self::$action = Helper::getPath($action);
			self::$params = $params;
			self::$defaults = $defaults;
		}
	}

	public static function request()
	{
		return self::$request;
	}

	public static function path()
	{
		return self::$path;
	}

	public static function route()
	{
		return self::$route;
	}

	public static function controller()
	{
		return self::$controller;
	}

	public static function page()
	{
		return self::$page;
	}

	public static function action()
	{
		return self::$action;
	}

	public static function params($index = null)
	{
		return (!empty(self::$params) and is_numeric($index)) ?
			((isset(self::$params[$index])) ? self::$params[$index] : null) :
			self::$params;
	}


}
