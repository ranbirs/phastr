<?php

namespace sys;

use sys\Load;
use sys\View;
use sys\Session;

use sys\utils\Helper;

class Init {

	protected static $load, $view, $session, $xhr;

	protected static $resource = array();

	private static $master, $controller, $error;

	public static function start()
	{
		Load::conf('constants');
		Load::vocab('sys', false);

		self::$view = new View();

		if (!self::_resource()) {
			self::$view->error(404, self::$error);
		}

		self::$session = new Session();
		self::$load = new Load();

		Load::conf('autoload');

		if (self::$resource['master']) {
			if (!in_array(self::$resource['controller'], Helper::getArray(\app\confs\sys\except__))) {
				self::$master = self::$load->controller(self::$resource['master']);
			}
			else {
				self::$resource['master'] = "";
			}
		}
		self::$controller = self::$load->controller(self::$resource['controller']);
	}

	private static function _resource()
	{
		$request = (isset($_SERVER['PATH_INFO'])) ?
			$_SERVER['PATH_INFO'] :
			((isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : null);

		$request = $path = strtolower(trim($request, "/"));

		$default = array(
			'master' => \app\confs\sys\master__,
			'autoload' => \app\confs\sys\autoload__,
			'homepage' => \app\confs\sys\homepage__,
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

		if (!Helper::resolveFilePath(\sys\app_base__ . "controllers/" . $route[0]))
			array_unshift($route, $default['autoload']);

		if (!isset($route[1]))
			$route[1] = $default['page'];
		if (!isset($route[2]))
			$route[2] = $default['action'];

		foreach ($route as $index => $param) {

			if ((strlen($param) > 128)) {
				self::$error = \app\vocabs\sys\er_icr__;
				return false;
			}
			if (preg_match('/[^a-z0-9-]/i', $param)) {
				self::$error = \app\vocabs\sys\er_icr__;
				return false;
			}
			if ($param === $default['method']) {
				self::$error = \app\vocabs\sys\er_icr__;
				return false;
			}

			switch ($index) {
				case 0:
					$controller = $param;
					$master = $default['master'];
					if ($master) {
						if ($controller === $master) {
							self::$error = \app\vocabs\sys\er_icc__;
							return false;
						}
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

		self::$resource = array(
			'request' => ($request) ? $request : "/",
			'path' => ($request) ? implode("/", $path) : $default['homepage'],
			'route' => "$controller/$page/$action",
			'master' => $master,
			'controller' => $controller,
			'page' => $page,
			'action' => $action,
			'params' => $params,
			'method' => $default['method']
		);

		return true;
	}

}
