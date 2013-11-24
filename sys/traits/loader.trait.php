<?php

namespace sys\traits;

use sys\utils\Helper;

trait Loader {

	public function includeFile($path, $ext = "php")
	{
		require_once Helper::getPath($path) . "." . $ext;
	}

	public function resolveFile($path, $base = app__, $ext = "php")
	{
		$path = Helper::getPath(\sys\base_path($path, $base)) . "." . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	public function resolveInclude($path, $type, $new = true, $base = app__, $ext = "php")
	{
		$this->includeFile(\sys\base_path($type . "s/". $path, $base), $ext);
		return ($new) ? $this->resolveInstance($path, $type, $base) : true;
	}

	public function resolveInstance($path, $type, $base = app__)
	{
		$path = Helper::getPath($type . "s/" . $path);
		$class = Helper::getPathClass("\\$base\\" . $path);
		return new $class;
	}

}
