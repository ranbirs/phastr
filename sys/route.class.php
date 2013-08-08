<?php

namespace sys;

use sys\Load;
use sys\utils\Helper;

class Route {

	const length__ = 128;

	private static $path, $route;
	public $defaults = array(
		'masters' => \app\confs\config\masters__,
		'autoload' => \app\confs\config\autoload__,
		'homepage' => \app\confs\config\homepage__,
		'page' => \app\confs\config\page__,
		'action' => \app\confs\config\action__,
		'method' => \app\confs\config\method__
	);
	public $error;

	function __construct()
	{
		$key = \app\confs\rewrite\name__;
		$path['request'] = (isset($_GET[$key])) ? Helper::getArray($_GET[$key], "/") : array();
		$path['path'] = $path['request'];
		unset($_GET[$key]);

		if (empty($path['request'])) {
			$path['path'] = array($this->defaults['autoload'], $this->defaults['homepage'], $this->defaults['action']);
			$path['request'] = "/";
		}
		$scope = Helper::getArray(\app\confs\config\controllers__, ",");
		$scope[] = $this->defaults['autoload'];

		if (!in_array(Helper::getPath($path['path'][0]), $scope))
			array_unshift($path['path'], $this->defaults['autoload']);

		if (!isset($path['path'][1]))
			$path['path'][1] = $this->defaults['page'];
		if (!isset($path['path'][2]))
			$path['path'][2] = $this->defaults['action'];

		$this->_parse($path);
	}

	private function _parse($path = array())
	{
		$route = array_splice($path['path'], 0, 3);
		$path['args'] = $path['path'];
		$path['path'] = array();
		$path['route'] = array();

		foreach ($route as $index => &$arg) {

			if ((strlen($arg) > self::length__)) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			$param = Helper::getPath($arg, 'path');
			$arg = Helper::getPath($arg);
			$path['route'][] = $param;

			if (preg_match('/[^a-z0-9-]/', $param)) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			if ($arg === $this->defaults['method']) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			switch ($index) {
				case 0:
					if ($arg === $this->defaults['masters']) {
						return $this->error = \sys\confs\error\route_controller__;
					}
					if ($arg !== $this->defaults['autoload'])
						$path['path'][] = $param;
					break;
				case 1:
					$path['path'][] = $param;
					break;
				case 2:
					if ($arg !== $this->defaults['action'])
						$path['path'][] = $param;
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

	public function args($index = null)
	{
		return (is_numeric($index)) ?
			((isset(self::$path['args'][$index])) ? self::$path['args'][$index] : null) :
			((is_null($index)) ? self::$path['args'] : null);
	}

}
