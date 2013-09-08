<?php

namespace sys;

use sys\Load;
use sys\utils\Helper;

class Route {

	const length__ = 128;

	private static $path, $route;
	public $error;

	function __construct()
	{
		$name = \app\confs\rewrite\name__;
		$path['request'] = (isset($_GET[$name])) ? Helper::getArray($_GET[$name], "/") : array();
		$path['path'] = $path['request'];
		unset($_GET[$name]);

		if (empty($path['request'])) {
			$path['path'] = array(\app\confs\config\autoload__, \app\confs\config\homepage__, \app\confs\config\action__);
			$path['request'] = "/";
		}
		$scope = Helper::getArray(\app\confs\config\controllers__, ",");
		$scope[] = \app\confs\config\autoload__;

		if (!in_array(Helper::getPath($path['path'][0]), $scope))
			array_unshift($path['path'], \app\confs\config\autoload__);

		if (!isset($path['path'][1]))
			$path['path'][1] = \app\confs\config\page__;
		if (!isset($path['path'][2]))
			$path['path'][2] = \app\confs\config\action__;

		$this->_parse($path);
	}

	private function _parse($path = array())
	{
		$path['route'] = array_splice($path['path'], 0, 3);
		$path['params'] = $path['path'];
		$path['path'] = array();
		$route = array();

		foreach ($path['route'] as $index => $arg) {

			if ((strlen($arg) > self::length__)) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			if (preg_match('/[^a-z0-9-]/', $arg)) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			$label = Helper::getPath($arg);
			$route[] = $label;

			if ($label === \app\confs\config\method__) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			switch ($index) {
				case 0:
					if ($label === \app\confs\config\masters__) {
						return $this->error = \sys\confs\error\route_controller__;
					}
					if ($label !== \app\confs\config\autoload__)
						$path['path'][] = $arg;
					break;
				case 1:
					$path['path'][] = $arg;
					break;
				case 2:
					if ($arg !== \app\confs\config\action__)
						$path['path'][] = $arg;
					break;
			}
		}
		$path['path'] = implode("/", $path['path']);
		$path['route'] = Helper::getPath($path['route'], 'route');
		self::$path = $path;
		self::$route = $route;
	}

	public function get()
	{
		return self::$path['route'];
	}

	public function path($request = false)
	{
		return (!$request) ? self::$path['path'] : self::$path['request'];
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
			((is_null($index)) ? self::$path['params'] : null);
	}

}
