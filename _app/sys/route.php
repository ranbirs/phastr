<?php

namespace sys;

use app\confs\Route as __route;

class Route
{

	const maxlength__ = 128;

	protected $path;

	function __construct()
	{	
		$path = parse_url($_SERVER['REQUEST_URI']);
		
		$path['file'] = $_SERVER['SCRIPT_NAME'];
		$path['base'] = (($path['base'] = dirname($path['file'])) == '/') ? '/' : $path['base'] . '/';
		$path['path'] = \sys\utils\helper\filter_split('/', substr($path['path'], strlen($path['file'])));
		$path['uri'] = ($path['path']) ? implode('/', $path['path']) : '/';
		
		if (!isset($path['path'][0])) {
			$path['path'][0] = __route::controller__;
		}
		elseif (!in_array($path['path'][0], \sys\utils\helper\filter_split(',', __route::controllers__))) {
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
		
		foreach ($path['route'] as &$arg) {
			if ((strlen($arg) > self::maxlength__) || preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
				return $this->error(404);
			}
			$path['label'][] = \sys\utils\path\label($arg);
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
		return ($key && isset($this->path[$key])) ? $this->path[$key] : ((!$key) ? $this->path : false);
	}

	public function params($index = null)
	{
		return (is_null($index)) ? $this->path['params'] : ((isset($this->path['params'][$index])) ? $this->path['params'][$index] : false);
	}

	public function controller($class = false)
	{
		return (!$class) ? $this->path['label'][0] : '\\' . app__ . '\\controllers\\' . $this->path['label'][0];
	}

	public function page()
	{
		return $this->path['label'][1];
	}

	public function action($method = false, $glue = __route::glue__)
	{
		return (!$method) ? $this->path['label'][2] : $this->path['label'][1] . $glue . $this->path['label'][2];
	}
	
	public function request()
	{
		if ($this->params(0) == __route::request__) {
			return $this->params(1);
		}
		return false;
	}

	public function error($code = 404, $message = '')
	{
		if ($message) {
			trigger_error($message);
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
