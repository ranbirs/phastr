<?php

namespace sys;

class Init {

	private static $view, $load, $xhr;

	private static $master, $controller, $error;
	private static $res = array();

	private static function _init()
	{
		\sys\Load::sys('view');

		self::$view = new \sys\View();

		if (!self::_resource()) {
			self::$view->error(404, self::$error);
		}

		\sys\Load::sys('utils/hash');

		\sys\Load::sys('session');
		\sys\Session::start();

		\sys\Load::sys('common');
		\sys\Load::sys('controller');
		\sys\Load::sys('model');

		\sys\Load::sys('modules/xhr');

		self::$load = new \sys\Load();
		self::$xhr = new \sys\modules\Xhr();
	}

	public static function start()
	{
		self::_init();

		\sys\Load::conf('autoload');

		if (self::$res['master']) {
			if (!in_array(self::$controller, \sys\utils\Helper::getArray(\app\confs\sys\except__))) {
				self::$master = \sys\Load::controller(self::$res['master']);
			}
			else {
				self::$res['master'] = "";
			}
		}
		self::$controller = \sys\Load::controller(self::$res['controller']);
	}

	public static function view()
	{
		return self::$view;
	}

	public static function load()
	{
		return self::$load;
	}

	public static function xhr()
	{
		return self::$xhr;
	}

	public static function res($key = null, $arg = null)
	{
		switch ($key) {
			case null:
				return self::$res;
			break;
			default:
				if (!isset(self::$res[$key])) {
					return false;
				}
			break;
			case 'params':
				if (is_numeric($arg)) {
					if (isset(self::$res[$key][$arg])) {
						return self::$res[$key][$arg];
					}
					return false;
				}
			break;
			case 'args':
				if (!isset(self::$res[$key])) {
					self::$res[$key] = \sys\utils\Helper::getArgs(self::$res['params']);
				}
				if ($arg) {
					if (isset(self::$res[$key][$arg])) {
						return self::$res[$key][$arg];
					}
					return false;
				}
			break;
		}
		return self::$res[$key];
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

		$resource = array_splice($path, 0, 3);
		$params = $path;
		$path = array();

		if (!\sys\utils\Helper::resolveFilePath(\sys\app_base__ . "controllers/" . $resource[0]))
			array_unshift($resource, $default['autoload']);

		if (!isset($resource[1]))
			$resource[1] = $default['page'];
		if (!isset($resource[2]))
			$resource[2] = $default['action'];

		foreach ($resource as $index => $param) {

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

		self::$res = array(
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
