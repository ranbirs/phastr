<?php

namespace sys;

use sys\configs\Route as __route;

class Route
{

	public $route;

	function __construct($resource, $action, array $routes, $deny = null) // @todo $deny action/arg wildcards
	{
		$route['file'] = $_SERVER['SCRIPT_NAME'];
		$route['base'] = dirname($route['file']);
		$route['uri'] = (isset($_SERVER['PATH_INFO'])) ? trim($_SERVER['PATH_INFO'], '/') : '';
		(strlen($route['uri'])) ? $route['path'] = explode('/', $route['uri']) : $route['uri'] = '/';
		
		if (!isset($route['path'][0])) {
			$route['path'][0] = $resource;
		}
		if (!isset($routes[$route['path'][0]])) {
			return $this->path = false;
		}
		if (!isset($route['path'][1])) {
			$route['path'][1] = $action;
		}
		$route['route'] = $route['path'];
		$route['params'] = array_splice($route['route'], 2);
		
		$route['label'][0] = basename($routes[$route['route'][0]]);
		$route['label'][1] = preg_replace('/[^a-z0-9_]/i', '_', $route['route'][1]);
		$route['class'] = '\\' . str_replace('/', '\\', $routes[$route['path'][0]]);
		
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
		return (!$class) ? $this->route['label'][0] : $this->route['class'];
	}

	public function action($label = false)
	{
		return (!$label) ? $this->route['route'][1] : $this->route['label'][1];
	}

	public function params($index = null)
	{
		return (!isset($index)) ? $this->route['params'] : ((isset($this->route['params'][$index])) ? $this->route['params'][$index] : false);
	}

	/*
	 * @param mixed|string $key
	 * @param bool $
	 * @return mixed|array|string self::path['params'] [$key index + 1 => value par] "array_slice" | value string ?:
	 * null ?: false
	 */
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

	public function error($code = 404, $view = null, $data = null)
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
