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

	public static function getPath($path = "", $type = 'label')
	{
		if (is_array($path))
			$path = implode("/", $path);
		$path = strtolower($path);

		switch ($type) {
			case 'label':
				$path = str_replace(array("--", "-"), array("__", "_"), $path);
				break;
			case 'path':
				$path = str_replace(array("__", "/", "_"), array("--", "--", "-"), $path);
				break;
			case 'tree':
				$path = str_replace(array("__", "--", "-"), array("/", "/", "_"), $path);
				break;
			case 'route':
				$path = (\app\confs\app\rewrite__) ? self::getPath($path, 'base') : self::getPath("", 'base') . "?_q=" . $path;
				break;
			case 'ajax':
				$path = \sys\Init::route()->get() . "/" . \sys\components\Request::param__ . "/" . $path;
				break;
			case 'base':
				$base = \app\confs\app\rewrite_base__;
				$path = ($base) ?
					(($path) ? "/" . $base . "/" . $path : "/" . $base . "/") :
					(($path) ? "/" . $path : "/");
				break;
		}
		return $path;
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
