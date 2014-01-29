<?php

namespace sys;

class Route
{
	use \sys\traits\Util;
	const rewrite__ = \app\confs\Route::rewrite__;
	const name__ = \app\confs\Route::name__;
	const base__ = \app\confs\Route::base__;
	const controllers__ = \app\confs\Route::controllers__;
	const autoload__ = \app\confs\Route::autoload__;
	const homepage__ = \app\confs\Route::homepage__;
	const page__ = \app\confs\Route::page__;
	const action__ = \app\confs\Route::action__;
	const method__ = \app\confs\Route::method__;
	const length__ = 128;

	protected $path;

	function __construct()
	{
		$name = self::name__;
		$path = (isset($_GET[$name])) ? $this->util()->helper()->splitString('/', $_GET[$name], 4) : [];
		$path = ['request' => $path, 'route' => $path];
		
		if (empty($path['request'])) {
			$path['route'] = [self::autoload__, self::homepage__, self::action__];
		}
		if (! in_array($path['route'][0], $this->util()->helper()->splitString(',', self::controllers__))) {
			array_unshift($path['route'], self::autoload__);
		}
		if (! isset($path['route'][1])) {
			$path['route'][1] = self::page__;
		}
		if (! isset($path['route'][2])) {
			$path['route'][2] = self::action__;
		}
		$path['params'] = current(array_slice($path['route'], 3));
		$path['route'] = array_slice($path['route'], 0, 3);
		
		foreach ($path['route'] as $index => &$arg) {
			if ((strlen($arg) > self::length__) || preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
				return $this->error(404);
			}
			if (($path['label'][$index] = $this->util()->helper()->path($arg)) == self::method__) {
				return $this->error(404);
			}
		}
		unset($arg);
		$path['path'] = $path['route'];
		if ($path['route'][2] == self::action__) {
			array_pop($path['path']);
		}
		if ($path['route'][0] == self::autoload__) {
			array_shift($path['path']);
		}
		$path['path'] = implode('/', $path['path']);
		$path['route'] = $this->util()->helper()->path($path['route'], 'route');
		$path['params'] = $this->util()->helper()->splitString('/', $path['params']);
		
		$this->path = $path;
	}

	public function path($request = false)
	{
		return (! $request) ? $this->path['path'] : $this->path['request'];
	}

	public function route($label = false)
	{
		return (! $label) ? $this->path['route'] : $this->path['label'];
	}

	public function params($index = null)
	{
		return (is_numeric($index)) ? ((isset($this->path['params'][$index])) ? $this->path['params'][$index] : null) : ((is_null(
			$index)) ? $this->path['params'] : false);
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

	public function method($methods = [])
	{
		if (self::method__) {
			$page[] = self::method__;
			$action[] = self::method__;
		}
		$page[] = $this->path['label'][1];
		$action[] = $this->path['label'][2];
		foreach ($page as $page_label) {
			foreach ($action as $action_label) {
				$methods[] = $page_label . '_' . $action_label;
			}
		}
		return $methods;
	}

	public function error($code = 404, $msg = '')
	{
		http_response_code($code = (int) $code);
		if (\app\confs\Config::errors__) {
			trigger_error(($msg) ? $msg : print_r(debug_backtrace(), true));
		}
		require app__ . '/views/layouts/error/' . $code . '.php';
		
		exit();
	}

}
