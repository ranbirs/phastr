<?php

namespace sys\utils;

use sys\Util;

class Loader extends Util
{

	public function resolveFile($path, $base = app__, $ext = 'php')
	{
		$path = $this->helper()->path($base . '/' . $path) . '.' . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	public function instanciate($path, $type, $instance = null, $base = app__)
	{
		$path = $this->helper()->path($type . 's/' . $path);
		$class = $this->helper()->pathClass('\\' . $base . '\\' . $path);
		
		if (! is_null($instance)) {
			$prop = $this->helper()->pathName($path);
			return $instance->$prop = new $class();
		}
		return new $class();
	}

}
