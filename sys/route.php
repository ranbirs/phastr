<?php

namespace sys;

use app\confs\Route as RouteConf;
use app\confs\Config as ConfigConf;

class Route
{
	
	use \sys\traits\util\Helper;
	use \sys\traits\util\Path {
		path as pathUtil;
	}

	const length__ = 128;

	protected $path;

	function __construct()
	{
		$name = RouteConf::name__;
		$path = (isset($_GET[$name])) ? $this->helper()->splitString('/', $_GET[$name], 4) : [];
		$path = ['request' => $path, 'route' => $path];
		
		if (empty($path['request'])) {
			$path['route'] = [RouteConf::autoload__, RouteConf::homepage__, RouteConf::action__];
		}
		if (!in_array($path['route'][0], $this->helper()->splitString(',', RouteConf::controllers__))) {
			array_unshift($path['route'], RouteConf::autoload__);
		}
		if (!isset($path['route'][1])) {
			$path['route'][1] = RouteConf::page__;
		}
		if (!isset($path['route'][2])) {
			$path['route'][2] = RouteConf::action__;
		}
		$path['params'] = current(array_slice($path['route'], 3));
		$path['route'] = array_slice($path['route'], 0, 3);
		
		foreach ($path['route'] as $index => &$arg) {
			if ((strlen($arg) > self::length__) || preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
				return $this->error(404);
			}
			if (($path['label'][$index] = $this->helper()->path($arg)) == RouteConf::method__) {
				return $this->error(404);
			}
		}
		unset($arg);
		$path['path'] = $path['route'];
		if ($path['route'][2] == RouteConf::action__) {
			array_pop($path['path']);
		}
		if ($path['route'][0] == RouteConf::autoload__) {
			array_shift($path['path']);
		}
		$path['path'] = implode('/', $path['path']);
		$path['route'] = $this->pathUtil()->uri(implode('/', $path['route']));
		$path['params'] = $this->helper()->splitString('/', $path['params']);
		
		$this->path = $path;
	}

	public function path($key = 'path')
	{
		return (isset($this->path[$key])) ? $this->path[$key] : false;
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

	public function methods($methods = [])
	{
		if (RouteConf::method__) {
			$page[] = RouteConf::method__;
			$action[] = RouteConf::method__;
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
		if (ConfigConf::errors__) {
			trigger_error(($msg) ? $msg : print_r(debug_backtrace(), true));
		}
		require app__ . '/views/layouts/error/' . $code . '.php';
		
		exit();
	}

}
