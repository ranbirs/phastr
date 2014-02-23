<?php

namespace sys\utils;

class Helper
{

	public function className($instance)
	{
		$class = explode('\\', get_class($instance));
		return end($class);
	}

	public function path($path = null, $type = 'label')
	{
		$path = implode('/', (array) $path);
		
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
			if (!strlen($param[0])) {
				continue;
			}
			$args[$param[0]] = (isset($param[1])) ? $param[1] : null;
		}
		return $args;
	}

	public function attr($attr = [], $glue = ' ')
	{
		$attrs = [];
		foreach ((array) $attr as $key => $val) {
			$val = (!is_array($val)) ? $val : implode($glue, $val);
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
		$array = (!($limit = (int) $limit)) ? explode($delimiter, $string) : explode($delimiter, $string, $limit);
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

}
