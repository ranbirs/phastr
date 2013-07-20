<?php

namespace sys;

use sys\Load;
use sys\components\Loader;
use sys\utils\Helper;

abstract class Res {

	protected static $request, $path, $route;
	protected static $controller, $page, $action, $params = array();
	protected static $error, $defaults = array();

	function __construct()
	{
		Load::conf('constants');
		Load::vocab('sys/error');

		$request_uri = $_SERVER['REQUEST_URI'];
		$script_name = $_SERVER['SCRIPT_NAME'];
		$script_name_length = strlen($script_name);

		if ($script_name === substr($request_uri, 0, $script_name_length))
			$request_uri = substr($request_uri, $script_name_length);
		$path_info = Helper::getArray(current(explode("?", $request_uri, 2)), "/");

		$defaults = array(
			'master' => \app\confs\sys\master__,
			'autoload' => \app\confs\sys\autoload__,
			'homepage' => \app\confs\sys\homepage__,
			'page' => \app\confs\sys\page__,
			'action' => \app\confs\sys\action__,
			'method' => \app\confs\sys\method__
		);
		$path = (!empty($path_info)) ? $path_info : array($defaults['autoload'], $defaults['homepage'], $defaults['action']);
		$route = array_splice($path, 0, 3);
		$params = $path;
		$path = array();

		$controllers = Helper::getArray(\app\confs\sys\controllers__, ",");
		$controllers[] = $defaults['autoload'];

		if (!in_array(Helper::getPath($route[0]), $controllers))
			array_unshift($route, $defaults['autoload']);

		if (!isset($route[1]))
			$route[1] = $defaults['page'];
		if (!isset($route[2]))
			$route[2] = $defaults['action'];

		foreach ($route as $index => &$param) {

			if (preg_match('/[^a-z0-9-]/i', $param)) {
				self::$error = \app\vocabs\sys\error\res_route__;
				break;
			}
			if ($param === $defaults['method']) {
				self::$error = \app\vocabs\sys\error\res_route__;
				break;
			}
			if ((strlen($param) > 128)) {
				self::$error = \app\vocabs\sys\error\res_route__;
				break;
			}

			switch ($index) {
				case 0:
					$controller = $param;
					$param = Helper::getPath($param);
					if ($param === $defaults['master']) {
						self::$error = \app\vocabs\sys\error\res_controller__;
						break 2;
					}
					if ($param !== $defaults['autoload'])
						$path[] = $controller;
					break;
				case 1:
					$page = $param;
					$path[] = $page;
					break;
				case 2:
					$action = $param;
					$param = Helper::getPath($param);
					if ($param !== $defaults['action'])
						$path[] = $action;
					break;
			}
		}
		unset($param);

		if (!isset(self::$error)) {
			$resource = array(
				'request' => $path_info,
				'path' => $path,
				'controller' => $controller,
				'page' => $page,
				'action' => $action,
				'params' => $params
			);
			self::_init($resource, $defaults);
		}
	}

	private static function _init($resource, $defaults)
	{
		self::$request = (!empty($resource['request'])) ? implode("/", $resource['request']) : "/";
		self::$path = (!empty($resource['path'])) ? implode("/", $resource['path']) : $defaults['homepage'];
		self::$route = Helper::getPath(array($resource['controller'], $resource['page'], $resource['action']), 'route');
		self::$controller = Helper::getPath($resource['controller']);
		self::$page = Helper::getPath($resource['page']);
		self::$action = Helper::getPath($resource['action']);
		self::$params = $resource['params'];
		self::$defaults = $defaults;
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
		return (is_numeric($index)) ?
			((isset(self::$params[$index])) ? self::$params[$index] : null) :
			self::$params;
	}

}
