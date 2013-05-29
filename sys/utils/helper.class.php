<?php

namespace sys\utils;

class Helper {

	public static function getFileName($path)
	{
		$path = explode("/", $path);
		return strtolower(end($path));
	}

	public static function resolveFilePath($path, $ext = ".php")
	{
		$path = self::getPath($path) . $ext;
		$file = stream_resolve_include_path($path);
		if ($file !== false) {
			return $path;
		}
		return false;
	}
	
	public static function requireFilePath($path, $ext = ".php")
	{
		require_once self::getPath($path) . $ext;
	}
	
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

	public static function resolveClassControl($path, $control = null, $base = 'app')
	{
		$path = self::getPath($path);
		$class = "\\$base\\" . self::getPathClass($path);

		switch ($control) {
			case 'instance':
				return new $class();
			case 'composite':
				$prop = self::getFileName($path);
				$inst = \sys\Compositor::instance();
				$inst->$prop = new $class();
		}
		return true;
	}
	
	public static function getPathClass($path)
	{
		$path = explode("/", strtolower($path));
		$class = ucfirst(array_pop($path));
		array_push($path, $class);
		return implode("\\", $path);
	}

	public static function getPath($path, $type = 'file')
	{
		$path = strtolower($path);

		switch ($type) {
			case 'file':
				return str_replace("-", "_", $path);
			case 'path':
				return str_replace("_", "-", $path);
			case 'tree':
				return str_replace(array("--", "-"), array("/", "_"), $path);
			case 'method':
				return str_replace(array("--", "-"), array("__", "_"), $path);
		}
	}

	public static function getArgs($params = null, $delimiter = ":")
	{
		$args = array();
		if (!is_array($params))
			$params = array($params);

		foreach ($params as $param) {
			$param = explode($delimiter, $param, 2);
			if ($param[0])
				$args[$param[0]] = (isset($param[1])) ? $param[1] : "";
		}
		return $args;
	}

	public static function getArray($string = null, $delimiter = ",")
	{
		return explode($delimiter, str_replace(" ", "", $string));
	}

}
