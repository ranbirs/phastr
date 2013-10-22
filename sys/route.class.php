<?php

namespace sys;

use sys\Load;
use sys\utils\Helper;

class Route {

	const rewrite__ = \app\confs\route\rewrite__;
	const name__ = \app\confs\route\name__;
	const base__ = \app\confs\route\base__;
	const controllers__ = \app\confs\route\controllers__;
	const masters__ = \app\confs\route\masters__;
	const autoload__ = \app\confs\route\autoload__;
	const homepage__ = \app\confs\route\homepage__;
	const page__ = \app\confs\route\page__;
	const action__ = \app\confs\route\action__;
	const method__ = \app\confs\route\method__;

	const length__ = 128;

	private static $route, $path;
	public $error;

	function __construct()
	{
		$name = self::name__;
		$path['request'] = (isset($_GET[$name])) ? Helper::getArray($_GET[$name], "/") : [];
		$path['path'] = $path['request'];
		unset($_GET[$name]);

		if (empty($path['request'])) {
			$path['path'] = [self::autoload__, self::homepage__, self::action__];
			$path['request'] = "/";
		}
		$scope = Helper::getArray(self::controllers__, ",");
		$scope[] = self::autoload__;

		if (!in_array(Helper::getPath($path['path'][0]), $scope))
			array_unshift($path['path'], self::autoload__);

		if (!isset($path['path'][1]))
			$path['path'][1] = self::page__;
		if (!isset($path['path'][2]))
			$path['path'][2] = self::action__;

		$this->_parse($path);
	}

	private function _parse($path = [])
	{
		$path['route'] = array_map('strtolower', array_splice($path['path'], 0, 3));
		$path['params'] = $path['path'];
		$path['path'] = [];
		$route = [];

		foreach ($path['route'] as $index => $arg) {

			if ((strlen($arg) > self::length__)) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			if (preg_match('/[^a-z0-9-]/', $arg)) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			$label = Helper::getPath($arg);
			$route[] = $label;

			if ($label === self::method__) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			switch ($index) {
				case 0:
					if ($label === self::masters__) {
						return $this->error = \sys\confs\error\route_controller__;
					}
					if ($label !== self::autoload__)
						$path['path'][] = $arg;
					break;
				case 1:
					$path['path'][] = $arg;
					break;
				case 2:
					if ($arg !== self::action__)
						$path['path'][] = $arg;
					break;
			}
		}
		$path['path'] = implode("/", $path['path']);
		$path['route'] = Helper::getPath($path['route'], 'route');
		self::$path = $path;
		self::$route = $route;
	}

	public function path($request = false)
	{
		return (!$request) ? self::$path['path'] : self::$path['request'];
	}

	public function route($labels = false)
	{
		return (!$labels) ? self::$path['route'] : self::$route;
	}

	public function controller()
	{
		return self::$route[0];
	}

	public function page()
	{
		return self::$route[1];
	}

	public function action()
	{
		return self::$route[2];
	}

	public function params($index = null)
	{
		return (is_numeric($index)) ?
			((isset(self::$path['params'][$index])) ? self::$path['params'][$index] : null) :
			((is_null($index)) ? self::$path['params'] : false);
	}

}
