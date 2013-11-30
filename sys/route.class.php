<?php

namespace sys;

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

	private static $path;
	public $error;

	function __construct()
	{
		$name = self::name__;
		$path = (isset($_GET[$name])) ? Helper::getArray('/', $_GET[$name], 4) : [];
		unset($_GET[$name]);

		$this->_parsePath($path);
		$this->_parseRoute($path);

		self::$path = $path;
	}

	private function _parsePath(&$path = [])
	{
		$path = ['request' => $path, 'path' => $path];

		if (empty($path['request'])) {
			$path['path'] = [self::autoload__, self::homepage__, self::action__];
			$path['request'] = '/';
		}
		$scope = Helper::getArray(',', self::controllers__);
		$scope[] = self::autoload__;

		if (!in_array(Helper::getPath($path['path'][0]), $scope))
			array_unshift($path['path'], self::autoload__);

		if (!isset($path['path'][1]))
			$path['path'][1] = self::page__;
		if (!isset($path['path'][2]))
			$path['path'][2] = self::action__;

		$path['route'] = array_splice($path['path'], 0, 3);
		$path['params'] = current($path['path']);
		$path['path'] = [];
		$path['label'] = [];
	}

	private function _parseRoute(&$path = [])
	{
		foreach ($path['route'] as $index => &$arg) {

			if ((strlen($arg) > self::length__)) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			if (preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			$path['label'][$index] = Helper::getPath($arg);

			if ($path['label'][$index] === self::method__) {
				return $this->error = \sys\confs\error\route_parse__;
			}
			switch ($index) {
				case 0:
					if ($path['label'][$index] === self::masters__) {
						return $this->error = \sys\confs\error\route_controller__;
					}
					if ($path['label'][$index] !== self::autoload__)
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
		unset($arg);

		$path['route'] = Helper::getPath($path['route'], 'route');
		$path['path'] = implode('/', $path['path']);
	}

	public function path($request = false)
	{
		return (!$request) ? self::$path['path'] : self::$path['request'];
	}

	public function route($label = false)
	{
		return (!$label) ? self::$path['route'] : self::$path['label'];
	}

	public function controller()
	{
		return self::$path['label'][0];
	}

	public function page()
	{
		return self::$path['label'][1];
	}

	public function action()
	{
		return self::$path['label'][2];
	}

	public function params($index = null)
	{
		if (!is_array(self::$path['params']))
			self::$path['params'] = Helper::getArray('/', self::$path['params']);

		return (is_numeric($index)) ?
			((isset(self::$path['params'][$index])) ? self::$path['params'][$index] : null) :
			((is_null($index)) ? self::$path['params'] : false);
	}

	public function error($code, $msg = '')
	{
		http_response_code($code = (int) $code);
		if ($msg and \app\confs\config\errors__) {
			trigger_error($msg);
		}
		require \sys\base_path('views/layouts/error/' . $code) . '.php';
		exit;
	}

}
