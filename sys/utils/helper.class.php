<?php

namespace sys\utils;

use sys\Util;

class Helper extends Util {

	use \sys\traits\Route;

	public function getInstanceClassName($instance)
	{
		return $this->getClassName(get_class($instance));
	}

	public function getClassName($class)
	{
		$class = explode('\\', $class);
		return end($class);
	}

	public function getClassPath($class)
	{
		$class = explode('\\', $class);
		return strtolower(implode('/', $class));
	}

	public function getPathName($path)
	{
		$path = explode('/', $path);
		return end($path);
	}

	public function getPathClass($path)
	{
		$path = explode('/', $path);
		return implode('\\', $path);
	}

	public function getPath($path = null, $type = 'label')
	{
		if (is_array($path))
			$path = implode('/', $path);

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
				$path = (\sys\Route::rewrite__) ?
					'/' . $this->getPath($path, 'base') :
					'/' . $this->getPath('', 'base') . '?' . \sys\Route::name__ . '=' . $path;
				break;
			case 'ajax':
				$path = $this->route()->route() . '/' . \sys\modules\Request::param__ . '/' . $path;
				break;
			case 'page':
				$path = $this->route()->controller() . '/' . $this->getPath(($path) ? $path : $this->route()->page(), 'tree');
				break;
			case 'base':
				$path = ($base = \sys\Route::base__) ?
					(($path) ? $base . '/' . $path : $base) :
					(($path) ? $path : '');
				break;
			case 'root':
				$path = ($path) ? $_SERVER['DOCUMENT_ROOT'] . '/' . $path : $_SERVER['DOCUMENT_ROOT'];
				break;
			default:
				return false;
		}
		return $path;
	}

	public function getArgs($params = null, $delimiter = ':')
	{
		$args = [];
		$params = (array) $params;

		foreach ($params as $param) {
			$param = array_map('trim', explode($delimiter, $param, 2));
			if (!strlen($param[0])) {
				continue;
			}
			$args[$param[0]] = (isset($param[1])) ? $param[1] : null;
		}
		return $args;
	}

	public function getAttr($attr = [], $glue = ' ')
	{
		$attrs = [];
		foreach ($attr as $key => $val) {
			$val = (!is_array($val)) ? $val : implode($glue, $val);
			if (is_int($key)) {
				$attrs[$val] = $val;
				continue;
			}
			$attrs[$key] = $val;
		}
		return $attrs;
	}

	public function getArray($delimiter, $string = '', $limit = null)
	{
		$limit = (int) $limit;
		$array = (!$limit) ? explode($delimiter, $string) : explode($delimiter, $string, $limit);
		$trim = function ($arg) use ($delimiter) {
			return trim($arg, $delimiter . ' ');
		};
		return array_values(array_filter(array_map($trim, $array), 'strlen'));
	}

	public function getStringArray($glue, $array = [], $prepend = '', $append = '')
	{
		$string_array = [];
		foreach ($array as $key => $val)
			$string_array[] = $prepend . $key . $glue . (string) $val . $append;
		return $string_array;
	}

}
