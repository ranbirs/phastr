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

	public static function getPathName($path)
	{
		$path = explode("/", $path);
		return strtolower(end($path));
	}

	public static function getPathClass($path)
	{
		$path = explode("/", strtolower($path));
		$class = ucfirst(array_pop($path));
		$path[] = $class;
		return implode("\\", $path);
	}

	public static function getPath($path = "", $type = 'label')
	{
		if (is_array($path))
			$path = implode("/", $path);
		$path = strtolower($path);

		switch ($type) {
			case 'label':
				$path = str_replace(["--", "-"], ["__", "_"], $path);
				break;
			case 'path':
				$path = str_replace(["__", "/", "_"], ["--", "--", "-"], $path);
				break;
			case 'tree':
				$path = str_replace(["__", "--", "-"], ["/", "/", "_"], $path);
				break;
			case 'route':
				$path = (\app\confs\rewrite\enabled__) ?
					self::getPath($path, 'base') :
					self::getPath("", 'base') . "?" . \app\confs\rewrite\name__ . "=" . $path;
				break;
			case 'ajax':
				$path = \sys\Init::route()->get() . "/" . \sys\modules\Request::param__ . "/" . $path;
				break;
			case 'base':
				$base = \app\confs\rewrite\base__;
				$path = ($base) ?
					(($path) ? "/" . $base . "/" . $path : "/" . $base . "/") :
					(($path) ? "/" . $path : "/");
				break;
			case 'root':
				$path = ($path) ? $_SERVER['DOCUMENT_ROOT'] . "/" . $path : $_SERVER['DOCUMENT_ROOT'];
				break;
		}
		return $path;
	}

	public static function getArgs($params = null, $delimiter = ":")
	{
		$args = [];
		if (!is_array($params))
			$params = [$params];

		foreach ($params as $param) {
			$param = array_map('trim', explode($delimiter, $param, 2));
			if (strlen($param[0]))
				$args[$param[0]] = (isset($param[1])) ? $param[1] : null;
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

	public static function getStringArray($array = [], $glue = ": ")
	{
		$string_array = [];
		foreach ($array as $key => $val)
			$string_array[] = $key . $glue . $val;
		return $string_array;
	}

}
