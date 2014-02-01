<?php

namespace sys\utils;

use sys\Util;

class Helper extends Util
{
	use \sys\traits\Route;

	public function instanceClassName($instance)
	{
		return $this->className(get_class($instance));
	}

	public function className($class)
	{
		$class = explode('\\', $class);
		return end($class);
	}

	public function classPath($class)
	{
		$class = explode('\\', $class);
		return strtolower(implode('/', $class));
	}

	public function pathName($path)
	{
		$path = explode('/', $path);
		return end($path);
	}

	public function pathClass($path)
	{
		$path = explode('/', $path);
		return implode('\\', $path);
	}

	public function path($path = null, $type = 'label')
	{
		if (is_array($path)) {
			$path = implode('/', $path);
		}
		switch ($type) {
			case 'label':
				$path = str_replace(['--', '-'], ['__', '_'], $path);
				break;
			case 'path':
				$path = str_replace(['__', '/', '_'], ['--', '--', '-'], $path);
				break;
			case 'tree':
				$path = str_replace(['__', '--', '-'], ['/', '/', '_'], $path);
				break;
			case 'route':
				$path = (\app\confs\Route::rewrite__) ? '/' . $this->path($path, 'base') : '/' . $this->path('', 'base') .
					 '?' . \app\confs\Route::name__ . '=' . $path;
				break;
			case 'ajax':
				$path = $this->route()->route() . '/' . \sys\modules\Request::param__ . '/' . $path;
				break;
			case 'page':
				$path = $this->route()->controller() . '/' . $this->path(($path) ? $path : $this->route()->page(), 
					'tree');
				break;
			case 'base':
				$path = ($base = \app\confs\Route::base__) ? (($path) ? $base . '/' . $path : $base) : (($path) ? $path : '');
				break;
			case 'root':
				$path = ($path) ? $_SERVER['DOCUMENT_ROOT'] . '/' . $path : $_SERVER['DOCUMENT_ROOT'];
				break;
			default:
				return false;
		}
		return $path;
	}

	public function args($params = null, $delimiter = ':')
	{
		$args = [];
		foreach ((array) $params as $param) {
			$param = array_map('trim', explode($delimiter, $param, 2));
			if (! strlen($param[0])) {
				continue;
			}
			$args[$param[0]] = (isset($param[1])) ? $param[1] : null;
		}
		return $args;
	}

	public function attr($attr = [], $glue = ' ')
	{
		$attrs = [];
		foreach ($attr as $key => $val) {
			$val = (! is_array($val)) ? $val : implode($glue, $val);
			if (is_int($key)) {
				$attrs[$val] = $val;
				continue;
			}
			$attrs[$key] = $val;
		}
		return $attrs;
	}

	public function splitString($delimiter, $string = '', $limit = null)
	{
		$array = (! ($limit = (int) $limit)) ? explode($delimiter, $string) : explode($delimiter, $string, $limit);
		$trim = function ($arg) use($delimiter)
		{
			return trim($arg, $delimiter . ' ');
		};
		return array_values(array_filter(array_map($trim, $array), 'strlen'));
	}

	public function composeArray($glue, $array = [], $prepend = '', $append = '')
	{
		$composed_array = [];
		foreach ($array as $key => $val) {
			$composed_array[] = $prepend . $key . $glue . (string) $val . $append;
		}
		return $composed_array;
	}

	public function isIndexArray($array = [])
	{
		return ($array === array_values($array));
	}

	public function shiftArrayIndex($array = [], $shift = 1)
	{
		if (($shift = (int) $shift) < 1) {
			return false;
		}
		$array = array_merge(array_fill(0, $shift, ''), array_values($array));
		return array_slice($array, $shift, null, true);
	}

}
