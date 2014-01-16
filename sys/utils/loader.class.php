<?php

namespace sys\utils;

class Loader extends \sys\Utils {

	public function includeFile($path, $ext = 'php')
	{
		require_once $this->helper->getPath($path) . '.' . $ext;
	}

	public function resolveFile($path, $base = app__, $ext = 'php')
	{
		$path = $this->helper->getPath(\sys\base_path($path, $base)) . '.' . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	public function resolveInclude($path, $type, $new = true, $base = app__, $ext = 'php')
	{
		$this->includeFile(\sys\base_path($type . 's/'. $path, $base), $ext);
		return ($new) ? $this->resolveInstance($path, $type, $base) : true;
	}

	public function resolveInstance($path, $type, $base = app__)
	{
		$path = $this->helper->getPath($type . 's/' . $path);
		$class = $this->helper->getPathClass('\\'. $base . '\\' . $path);
		return new $class;
	}

}
