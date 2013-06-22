<?php

namespace sys\utils;

class Helper {

	public static function getClassName($class)
	{
		$class = explode("\\", $class);
		return ucfirst(strtolower(end($class)));
	}

	public static function getClassPath($class)
	{
		$class = explode("\\", $class);
		return strtolower(implode("/", $class));
	}

	public static function getPathClass($path)
	{
		$path = explode("/", strtolower($path));
		$class = ucfirst(array_pop($path));
		array_push($path, $class);
		return implode("\\", $path);
	}

	public static function getPathName($path)
	{
		$path = explode("/", $path);
		return strtolower(end($path));
	}

	public static function getPath($path = "", $type = 'file')
	{
		switch ($type) {
			case 'file':
				$path = str_replace("-", "_", $path);
				break;
			case 'path':
				$path = str_replace("_", "-", $path);
				break;
			case 'tree':
				$path = str_replace(array("--", "-"), array("/", "_"), $path);
				break;
			case 'method':
				$path = str_replace(array("--", "-"), array("__", "_"), $path);
				break;
			case 'route':
				$path = "/" . $path;
				if (!\app\confs\app\rewrite__)
					$path = $_SERVER['SCRIPT_NAME'] . "?" . \app\confs\sys\query_str__ . "=" . $path;
				break;
		}
		return strtolower($path);
	}

	public static function getArgs($params = null, $delimiter = ":")
	{
		$args = array();
		if (!is_array($params))
			$params = array($params);

		foreach ($params as $param) {
			$param = array_map('trim', explode($delimiter, $param, 2));
			if ($param[0])
				$args[$param[0]] = (isset($param[1])) ? $param[1] : "";
		}
		return $args;
	}

	public static function getArray($string = null, $delimiter = ",")
	{
		$array = explode($delimiter, $string);
		$trim = function ($arg) use ($delimiter) {
			return trim($arg, $delimiter . " ");
		};
		$filter = function ($arg) {
			return ($arg or is_numeric($arg));
		};
		return array_values(array_filter(array_map($trim, $array), $filter));
	}

}
