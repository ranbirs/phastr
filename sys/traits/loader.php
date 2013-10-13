<?php

namespace sys\traits;

use sys\Init;
use sys\utils\Helper;

trait Loader {

	protected function load($type, $path, $args = null)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = Init::load()->get($type, $path, $args);
	}

	protected function includeFile($path, $ext = "php")
	{
		require_once Helper::getPath($path) . "." . $ext;
	}

	protected function resolveFile($path, $base = app__, $ext = "php")
	{
		$path = Helper::getPath(\sys\base_path($path, $base)) . "." . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

	protected function resolveInclude($path, $subj, $new = true, $base = app__, $ext = "php")
	{
		$this->includeFile(\sys\base_path($subj . "s/". $path, $base), $ext);
		return ($new) ? $this->resolveInstance($path, $subj, $base) : true;
	}

	protected function resolveInstance($path, $subj, $base = app__)
	{
		$path = Helper::getPath($subj . "s/" . $path);
		$class = Helper::getPathClass("\\$base\\" . $path);
		return new $class;
	}

}
