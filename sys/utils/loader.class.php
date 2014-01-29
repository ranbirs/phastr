<?php

namespace sys\utils;

use sys\Util;

class Loader extends Util
{

	public function includeFile($path, $ext = 'php')
	{
		require_once $this->helper()->path($path) . '.' . $ext;
	}

	public function resolveFile($path, $base = app__, $ext = 'php')
	{
		$path = $this->helper()->path($base . '/' . $path) . '.' . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	public function resolveInclude($path, $type, $new = true, $instance = null, $base = app__, $ext = 'php')
	{
		$this->includeFile($base . '/' . $type . 's/' . $path, $ext);
		return ($new) ? $this->resolveInstance($path, $type, $instance, $base) : true;
	}

	public function resolveInstance($path, $type, $instance = null, $base = app__)
	{
		$path = $this->helper()->path($type . 's/' . $path);
		$class = $this->helper()->pathClass('\\' . $base . '\\' . $path);
		
		if (is_object($instance)) {
			$prop = $this->helper()->pathName($path);
			return $instance->$prop = new $class();
		}
		return new $class();
	}

}
