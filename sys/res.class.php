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

		$key = \app\confs\sys\query_str__;
		$request = (isset($_GET[$key])) ? Helper::getArray($_GET[$key], "/") : array();
		$defaults = array(
			'master' => \app\confs\sys\master__,
			'autoload' => \app\confs\sys\autoload__,
			'homepage' => \app\confs\sys\homepage__,
			'page' => \app\confs\sys\page__,
			'action' => \app\confs\sys\action__,
			'method' => \app\confs\sys\method__
		);
		$path = (!empty($request)) ? $request : array($defaults['autoload'], $defaults['homepage'], $defaults['action']);
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
					if ($controller === $defaults['master']) {
						self::$error = \app\vocabs\sys\error\res_controller__;
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
			$resource = array(
				'request' => $request,
				'path' => $path,
				'controller' => $controller,
				'page' => $page,
				'action' => $action,
				'params' => $params
			);
			self::_build($resource, $defaults);
		}
	}

	private static function _build($resource, $defaults)
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
		return (!empty(self::$params) and is_numeric($index)) ?
			((isset(self::$params[$index])) ? self::$params[$index] : null) :
			self::$params;
	}

}
