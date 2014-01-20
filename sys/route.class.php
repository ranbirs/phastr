<?php

namespace sys;

class Route {

	use \sys\traits\Util;

	const rewrite__ = \app\confs\route\rewrite__;
	const name__ = \app\confs\route\name__;
	const base__ = \app\confs\route\base__;
	const controllers__ = \app\confs\route\controllers__;
	const autoload__ = \app\confs\route\autoload__;
	const homepage__ = \app\confs\route\homepage__;
	const page__ = \app\confs\route\page__;
	const action__ = \app\confs\route\action__;
	const method__ = \app\confs\route\method__;

	const length__ = 128;

	protected $path;

	function __construct()
	{
		$name = self::name__;
		$path = (isset($_GET[$name])) ? $this->util()->helper()->getArray('/', $_GET[$name], 4) : [];
		unset($_GET[$name]);

		$this->path = $this->_parsePath($path);
	}

	private function _parsePath($path = [])
	{
		$path = ['request' => $path, 'path' => $path];

		if (empty($path['request'])) {
			$path['path'] = [self::autoload__, self::homepage__, self::action__];
			$path['request'] = '/';
		}
		$scope = $this->util()->helper()->getArray(',', self::controllers__);
		$scope[] = self::autoload__;

		if (!in_array($this->util()->helper()->getPath($path['path'][0]), $scope))
			array_unshift($path['path'], self::autoload__);

		if (!isset($path['path'][1]))
			$path['path'][1] = self::page__;
		if (!isset($path['path'][2]))
			$path['path'][2] = self::action__;

		$path['route'] = array_splice($path['path'], 0, 3);
		$path['params'] = current($path['path']);
		$path['path'] = [];
		$path['label'] = [];

		foreach ($path['route'] as $index => &$arg) {

			if ((strlen($arg) > self::length__) || preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
				return $this->error(404);
			}
			if (($path['label'][$index] = $this->util()->helper()->getPath($arg)) == self::method__) {
				return $this->error(404);
			}
			switch ($index) {
				case 0:
					if ($path['label'][$index] != self::autoload__)
						$path['path'][] = $arg;
					break;
				case 1:
					$path['path'][] = $arg;
					break;
				case 2:
					if ($arg != self::action__)
						$path['path'][] = $arg;
					break;
			}
		}
		unset($arg);

		$path['route'] = $this->util()->helper()->getPath($path['route'], 'route');
		$path['path'] = implode('/', $path['path']);

		return $path;
	}

	public function path($request = false)
	{
		return (!$request) ? $this->path['path'] : $this->path['request'];
	}

	public function route($label = false)
	{
		return (!$label) ? $this->path['route'] : $this->path['label'];
	}

	public function controller()
	{
		return $this->path['label'][0];
	}

	public function page()
	{
		return $this->path['label'][1];
	}

	public function action()
	{
		return $this->path['label'][2];
	}

	public function params($index = null)
	{
		if (!is_array($this->path['params']))
			$this->path['params'] = $this->util()->helper()->getArray('/', $this->path['params']);

		return (is_numeric($index)) ?
			((isset($this->path['params'][$index])) ? $this->path['params'][$index] : null) :
			((is_null($index)) ? $this->path['params'] : false);
	}

	public function error($code = 404, $msg = '')
	{
		http_response_code($code = (int) $code);
		if (\app\confs\config\errors__) {
			trigger_error(($msg) ? $msg : print_r(debug_backtrace(), true));
		}
		require \sys\base_path('views/layouts/error/' . $code) . '.php';
		exit;
	}

}
