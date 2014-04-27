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
		$path['script'] = $this->helper()->splitString('/', $_SERVER['SCRIPT_NAME']);
		$path['request'] = $this->helper()->splitString('/', $_SERVER['REQUEST_URI']);
		$path['route'] = array_values(array_diff_assoc($path['request'], $path['script']));
		$path['base'] = trim(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME), '/');
		$path['script'] = implode($path['script']);

		if (!isset($path['route'][0])) {
			$path['route'][0] = RouteConf::controller__;
		}
		elseif (!in_array($path['route'][0], $this->helper()->splitString(',', RouteConf::scope__))) {
			return $this->error(404);
		}
		if (!isset($path['route'][1])) {
			$path['route'][1] = RouteConf::page__;
		}
		if (!isset($path['route'][2])) {
			$path['route'][2] = RouteConf::action__;
		}
		$path['params'] = (isset($path['route'][3])) ? array_splice($path['route'], 3) : [];

		foreach ($path['route'] as $index => &$arg) {
			if ((strlen($arg) > self::length__) || preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
				return $this->error(404);
			}
			if (($path['label'][$index] = $this->helper()->path($arg)) == RouteConf::method__) {
				return $this->error(404);
			}
		}
		unset($arg);
		$path['path'] = implode('/', $path['route']);
		$path['route'] = implode('/', $path['route']);
		
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
		if ($msg) {
			trigger_error($msg);
		}
		$this->status($code);
		require app__ . '/views/layouts/error/' . $code . '.php';
		
		exit();
	}
	
	public function status($code = 200, $headers = [])
	{
		return ($code) ? http_response_code($code) : http_response_code();
	}

}
