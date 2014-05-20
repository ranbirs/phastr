<?php

namespace sys;

use app\confs\Route as __route;
use app\confs\Config as __config;

class Route
{
	
	use \sys\traits\util\Helper;

	const length__ = 128;

	protected $path;

	function __construct()
	{
		$path = parse_url($_SERVER['REQUEST_URI']);
		
		$path['path'] = $this->helper()->splitString('/', $path['path']);
		$path['file'] = $this->helper()->splitString('/', $_SERVER['SCRIPT_NAME']);
		$path['path'] = array_values(array_diff_assoc($path['path'], $path['file']));
		$path['uri'] = (!empty($path['path'])) ? implode('/', $path['path']) : '/';
		$path['base'] = implode('/', array_slice($path['file'], 0, -1));
		$path['file'] = implode('/', $path['file']);
		
		if (!isset($path['path'][0])) {
			$path['path'][0] = __route::controller__;
		}
		elseif (!in_array($path['path'][0], $this->helper()->splitString(',', __route::scope__))) {
			return $this->error(404);
		}
		if (!isset($path['path'][1])) {
			$path['path'][1] = __route::page__;
		}
		if (!isset($path['path'][2])) {
			$path['path'][2] = __route::action__;
		}
		$path['params'] = (isset($path['path'][3])) ? array_slice($path['path'], 3) : [];
		$path['route'] = array_slice($path['path'], 0, 3);
		
		foreach ($path['route'] as $index => &$arg) {
			if ((strlen($arg) > self::length__) || preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
				return $this->error(404);
			}
			if (($path['label'][$index] = $this->helper()->path($arg)) == __route::method__) {
				return $this->error(404);
			}
		}
		unset($arg);
		$path['route'] = implode('/', $path['route']);
		$path['path'] = implode('/', $path['path']);
		
		$this->path = $path;
	}

	public function uri()
	{
		return $this->path['uri'];
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

	public function controller($class = false)
	{
		return (!$class) ? $this->path['label'][0] : $this->helper()->classFullName($this->path['label'][0], 
			'controllers');
	}

	public function page()
	{
		return $this->path['label'][1];
	}

	public function action($method = false)
	{
		if (!$method) {
			return $this->path['label'][2];
		}
		if (($method = __route::method__)) {
			$page[] = $method;
			$action[] = $method;
		}
		$page[] = $this->path['label'][1];
		$action[] = $this->path['label'][2];
		
		$method = array();
		foreach ($page as $page_label) {
			foreach ($action as $action_label) {
				$method[] = $page_label . '_' . $action_label;
			}
		}
		return $method;
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

	public function status($code = 200)
	{
		return ($code) ? http_response_code($code) : http_response_code();
	}

}
