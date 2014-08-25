<?php

namespace sys;

use app\configs\Route as __route;

class Route
{

	public $path;

	function __construct()
	{
		$path['file'] = $_SERVER['SCRIPT_NAME'];
		$path['base'] = rtrim(dirname($path['file']), '/') . '/';
		$path['path'] = (isset($_SERVER['PATH_INFO'])) ? \sys\utils\helper\filter_split('/', $_SERVER['PATH_INFO'], 4) : [];
		$path['uri'] = ($path['path']) ? implode('/', $path['path']) : '/';

		if (!isset($path['path'][0])) {
			$path['path'][0] = __route::controller__;
		}
		elseif (!in_array($path['path'][0], explode(',', __route::scope__))) {
			return $this->error(404);
		}
		if (!isset($path['path'][1])) {
			$path['path'][1] = __route::page__;
		}
		if (!isset($path['path'][2])) {
			$path['path'][2] = __route::action__;
		}
		$path['route'] = array_slice($path['path'], 0, 3);
		$path['params'] = (isset($path['path'][3])) ? explode('/', trim($path['path'][3],'/')) : [];
		
		foreach ($path['route'] as &$arg) {
			if ((strlen($arg) > __route::length__) || preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
				return $this->error(404);
			}
			$path['label'][] = str_replace('-', '_', $arg);
		}
		unset($arg);

		$path['path'] = implode('/', $path['path']);
		$path['route'] = implode('/', $path['route']);

		$path['class'] = '\\app\\controllers\\' . $path['label'][0];
		$path['method'] = $path['label'][1] . __route::glue__ . $path['label'][2];

		$this->path = $path;
	}

	public function uri()
	{
		return $this->path['uri'];
	}

	public function path($key = 'path')
	{
		return ($key && isset($this->path[$key])) ? $this->path[$key] : ((!$key) ? $this->path : false);
	}

	public function controller($class = false)
	{
		return (!$class) ? $this->path['label'][0] : $this->path['class'];
	}

	public function page()
	{
		return $this->path['label'][1];
	}

	public function action($method = false)
	{
		return (!$method) ? $this->path['label'][2] : $this->path['method'];
	}
	
	public function params($index = null)
	{
		return (!isset($index)) ? $this->path['params'] : ((isset($this->path['params'][$index])) ? $this->path['params'][$index] : false);
	}
	
	public function request($param = __route::request__)
	{
		return (isset($this->path['params'][1]) && $this->path['params'][0] == $param) ? $this->path['params'][1] : false;
	}

	public function error($code = 404, $message = null)
	{
		if ($message) {
			trigger_error($message);
		}
		$this->status($code);

		require app__ . '/views/layouts/error/' . $code . '.php';
		
		exit();
	}

	public function status($code = null)
	{
		return ($code) ? http_response_code($code) : http_response_code();
	}

}
