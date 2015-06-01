<?php

namespace sys;

class Route
{

	public $route;

	function __construct($resource, $action, array $routes, $deny = null) // @todo
	{
		$route['file'] = $_SERVER['SCRIPT_NAME'];
		$route['base'] = dirname($route['file']);
		$route['uri'] = (isset($_SERVER['PATH_INFO'])) ? trim($_SERVER['PATH_INFO'], '/') : '';
		(strlen($route['uri'])) ? $route['path'] = explode('/', $route['uri']) : $route['uri'] = '/';
		
		if (!isset($route['path'][0])) {
			$route['path'][0] = $resource;
		}
		if (!isset($route['path'][1])) {
			$route['path'][1] = $action;
		}
		$route['resource'] = (isset($routes[$route['path'][0]])) ? $routes[$route['path'][0]] : null;
		$route['route'] = $route['path'];
		$route['params'] = array_splice($route['route'], 2);
		$route['path'] = implode('/', $route['path']);
		
		$this->route = $route;
	}

	public function uri()
	{
		return $this->route['uri'];
	}

	public function route($key = 'path', $join = false)
	{
		return (isset($this->route[$key])) ? ((!$join) ? $this->route[$key] : implode('/', (array) $this->route[$key])) : false;
	}

	public function resource($class = false)
	{
		if (!$this->route['resource']) {
			return false;
		}
		return (!$class) ? basename($this->route['resource']) : '\\' . str_replace('/', '\\', $this->route['resource']);
	}

	public function action($search = null, $replace = null)
	{
		return (!isset($search)) ? $this->route['route'][1] : str_replace($search, $replace, $this->route['route'][1]);
	}

	public function params($index = null)
	{
		return (!isset($index)) ? $this->route['params'] : ((isset($this->route['params'][$index])) ? $this->route['params'][$index] : false);
	}

	public function arg($key, $pair = false)
	{
		if (($index = array_search($key, $this->route['params'])) === false) {
			return false;
		}
		return ($slice = array_slice($this->route['params'], $index + 1, 1, true)) ? ((!$pair) ? current($slice) : $slice) : null;
	}

	public function status($code = null)
	{
		return (!isset($code)) ? http_response_code() : http_response_code($code);
	}

	public function error($code, $view = null, $data = null)
	{
		$this->status($code);
		
		if (isset($view)) {
			if (isset($data)) {
				extract($data);
			}
			require $view . '.php';
		}
		exit();
	}

}
