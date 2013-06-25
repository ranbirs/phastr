<?php

namespace sys;

use sys\components\Loader;
use sys\utils\Helper;

class Res {

	protected static $resource = array();
	protected static $error;

	protected static function init()
	{
		$qs = \app\confs\sys\query_str__;
		$request = (isset($_GET[$qs])) ? $_GET[$qs] : null;
		$request = $path = strtolower(trim($request, "/"));

		$default = array(
			'autoload' => \app\confs\app\autoload__,
			'homepage' => \app\confs\app\homepage__,
			'page' => \app\confs\sys\page__,
			'action' => \app\confs\sys\action__,
			'method' => \app\confs\sys\method__,
			'master' => \app\confs\sys\master__
		);

		if (!$path)
			$path = $default['autoload'] . "/" . $default['homepage'];
		$path = explode("/", $path);

		$route = array_splice($path, 0, 3);
		$params = $path;
		$path = array();

		if (!Loader::resolveFile("controllers/" . $route[0]))
			array_unshift($route, $default['autoload']);

		if (!isset($route[1]))
			$route[1] = $default['page'];
		if (!isset($route[2]))
			$route[2] = $default['action'];

		foreach ($route as $index => $param) {

			if ($param === $default['method']) {
				self::$error = \app\vocabs\sys\er_icr__;
				return false;
			}
			if (preg_match('/[^a-z0-9-]/i', $param)) {
				self::$error = \app\vocabs\sys\er_icr__;
				return false;
			}
			if ((strlen($param) > 128)) {
				self::$error = \app\vocabs\sys\er_icr__;
				return false;
			}

			switch ($index) {
				case 0:
					$controller = $param;
					if ($controller === $default['master']) {
						self::$error = \app\vocabs\sys\er_icc__;
						return false;
					}
					if ($controller !== $default['autoload'])
						$path[] = $controller;
					break;
				case 1:
					$page = $param;
					$path[] = $page;
					break;
				case 2:
					$action = $param;
					if ($action !== $default['action'])
						$path[] = $action;
					break;
			}
		}
		return array(
			'request' => ($request) ? $request : "/",
			'path' => ($request) ? implode("/", $path) : $default['homepage'],
			'route' => Helper::getPath("$controller/$page/$action", 'route'),
			'controller' => Helper::getPath($controller),
			'page' => Helper::getPath($page),
			'action' => Helper::getPath($action),
			'params' => $params,
			'default' => $default
		);
	}

	public static function request()
	{
		return self::$resource['request'];
	}

	public static function path()
	{
		return self::$resource['path'];
	}

	public static function route()
	{
		return self::$resource['route'];
	}

	public static function controller()
	{
		return self::$resource['controller'];
	}

	public static function page()
	{
		return self::$resource['page'];
	}

	public static function action()
	{
		return self::$resource['action'];
	}

	public static function params($index = null)
	{
		return (is_numeric($index)) ?
			((isset(self::$resource['params'][$index])) ? self::$resource['params'][$index] : null) :
			self::$resource['params'];
	}

	public static function args($key = null)
	{
		if (!isset(self::$resource['args']))
			self::$resource['args'] = Helper::getArgs(self::$resource['params']);
		return ($key) ?
			((isset(self::$resource['args'][$key])) ? self::$resource['args'][$key] : null) :
			self::$resource['args'];
	}


}
