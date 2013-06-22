<?php

namespace sys;

use sys\Call;
use sys\View;
use sys\Session;
use sys\components\Loader;
use sys\components\Load;
use sys\utils\Helper;

class Init {

	protected static $view, $load, $session, $xhr;
	protected static $resource = array();
	private static $error;

	public static function start()
	{
		Call::conf('constants');
		Call::vocab('sys');

		self::$view = new View();
		self::$resource = self::_resource();

		if (!self::$resource) {
			self::$view->error(404, self::$error);
		}
		self::$load = new Load();
		self::$session = new Session();

		Call::conf('autoload');

		if (self::$resource['master']) {
			if (!in_array(self::$resource['controller'], Helper::getArray(\app\confs\sys\except__)))
				Call::controller(self::$resource['master']);
		}
		Call::controller(self::$resource['controller'])->method(self::$resource);
	}

	private static function _resource()
	{
		$qs = \app\confs\sys\query_str__;
		$request = (isset($_GET[$qs])) ? $_GET[$qs] : null;
		$request = $path = strtolower(trim($request, "/"));

		$default = array(
			'autoload' => \app\confs\app\autoload__,
			'homepage' => \app\confs\app\homepage__,
			'page' => \app\confs\sys\page__,
			'action' => \app\confs\sys\action__,
			'method' => \app\confs\sys\method__
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
					$master = \app\confs\sys\master__;
					if ($controller === $master) {
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
			'controller' => $controller,
			'page' => $page,
			'action' => $action,
			'params' => $params,
			'master' => $master,
			'default' => $default
		);
	}

}
