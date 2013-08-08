<?php

namespace sys\components;

use sys\utils\Helper;

trait Loader {

	public function includeFile($path, $ext = ".php")
	{
		require_once Helper::getPath($path) . $ext;
	}

	public function resolveFile($path, $base = app__, $ext = ".php")
	{
		$path = Helper::getPath(\sys\base($path, $base)) . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	public function resolveInclude($path, $context, $instance = true, $base = app__, $ext = ".php")
	{
		$this->includeFile(\sys\base($context . "s/". $path, $base), $ext);
		return ($instance) ? $this->_resolveInstance($path, $context, $base) : true;
	}

	private function _resolveInstance($path, $context, $base = app__)
	{
		$path = Helper::getPath($context . "s/" . $path);
		$class = Helper::getPathClass("\\$base\\" . $path);
		return new $class;
	}

}
