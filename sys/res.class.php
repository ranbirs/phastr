<?php

namespace sys;

use sys\Load;
use sys\components\Loader;
use sys\utils\Helper;

abstract class Res {

	protected static $script, $request, $path, $route;
	protected static $error, $defaults = array();

	function __construct()
	{
		$this->_init();
	}

	private function _init()
	{
		Load::conf('constants');
		Load::vocab('sys/error');

		$request['script'] = trim($_SERVER['SCRIPT_NAME'], "/");
		$request['uri'] = trim($_SERVER['REQUEST_URI'], "/");

		if ($request['script'] === substr($request['uri'], 0, strlen($request['script'])))
			$request['uri'] = substr($request['uri'], strlen($request['script']));

		$request['uri'] = explode("?", $request['uri'], 2);
		$request['path'] = Helper::getArray($request['uri'][0], "/");
		$request['qstr'] = array();
		if (isset($request['uri'][1]))
			parse_str($request['uri'][1], $request['qstr']);

		$defaults = array(
			'master' => \app\confs\sys\master__,
			'autoload' => \app\confs\sys\autoload__,
			'homepage' => \app\confs\sys\homepage__,
			'page' => \app\confs\sys\page__,
			'action' => \app\confs\sys\action__,
			'method' => \app\confs\sys\method__
		);
		if (!empty($request['path'])) {
			$path = $request['path'];
			$request['path'] = implode("/", $path);
		}
		else {
			$path = array($defaults['autoload'], $defaults['homepage'], $defaults['action']);
			$request['path'] = "/";
		}
		$route = array_map('strtolower', array_splice($path, 0, 3));
		$request['params'] = $path;

		$scope = Helper::getArray(\app\confs\sys\controllers__, ",");
		$scope[] = $defaults['autoload'];

		if (!in_array(Helper::getPath($route[0]), $scope))
			array_unshift($route, $defaults['autoload']);

		if (!isset($route[1]))
			$route[1] = $defaults['page'];
		if (!isset($route[2]))
			$route[2] = $defaults['action'];

		$request['route'] = Helper::getpath($route, 'route');
		$path = array();

		foreach ($route as $index => &$arg) {

			if (preg_match('/[^a-z0-9-]/', $arg)) {
				return self::$error = \app\vocabs\sys\error\res_route__;
			}
			if ($arg === $defaults['method']) {
				return self::$error = \app\vocabs\sys\error\res_route__;
			}
			if ((strlen($arg) > 128)) {
				return self::$error = \app\vocabs\sys\error\res_route__;
			}
			$param = $arg;
			$arg = Helper::getPath($arg);

			switch ($index) {
				case 0:
					if ($arg === $defaults['master']) {
						return self::$error = \app\vocabs\sys\error\res_controller__;
					}
					if ($arg !== $defaults['autoload'])
						$path[] = $param;
					break;
				case 1:
					$path[] = $param;
					break;
				case 2:
					if ($arg !== $defaults['action'])
						$path[] = $param;
					break;
			}
		}
		unset($arg);

		self::$script = $script;
		self::$request = $request;
		self::$path = implode("/", $path);
		self::$route = $route;
		self::$defaults = $defaults;
	}

	public static function request($context = 'path')
	{
		return (isset(self::$request[$context])) ? self::$request[$context] : null;
	}

	public static function path()
	{
		return self::$path;
	}

	public static function route()
	{
		return self::$request['route'];
	}

	public static function controller()
	{
		return self::$route[0];
	}

	public static function page()
	{
		return self::$route[1];
	}

	public static function action()
	{
		return self::$route[2];
	}

	public static function params($index = null)
	{
		return (is_numeric($index)) ?
			((isset(self::$request['params'][$index])) ? self::$request['params'][$index] : null) :
			self::$request['params'];
	}

}
