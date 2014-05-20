<?php

namespace sys\utils;

use app\confs\Route as __Route;

class Path
{
	
	use \sys\traits\Route;
	use \sys\traits\util\Helper;

	public function resolve($file)
	{
		$file = stream_resolve_include_path($file);
		return ($file !== false) ? $file : false;
	}

	public function file($path, $base = app__, $ext = 'php')
	{
		return $this->helper()->path($base . '/' . $path) . '.' . $ext;
	}

	public function root($path = '')
	{
		return ($path) ? $_SERVER['DOCUMENT_ROOT'] . '/' . $path : $_SERVER['DOCUMENT_ROOT'];
	}

	public function base($path = '')
	{
		return ($base = $this->route()->path('base')) ? (($path) ? $base . '/' . $path : $base) : (($path) ? $path : '');
	}

	public function page($path = '')
	{
		return $this->route()->controller() . '/' .
			 $this->helper()->path(($path) ? $path : $this->route()->page(), 'tree');
	}

	public function uri($path = '')
	{
		$path = ($path && $path != '/') ? '/' . $path : '';
		$base = (__route::rewrite__) ? (($base = $this->route()->path('base')) ? '/' . $base : '') : '/' .
			 $this->route()->path('file');
		return $base . $path;
	}

	public function request($path = '')
	{
		return $this->uri($this->route()->path('route') . '/' . \sys\modules\Request::param__ . '/' . $path);
	}

	public function trail($path = '')
	{
		return ($path) ? $path . __route::trail__ : '';
	}

}
