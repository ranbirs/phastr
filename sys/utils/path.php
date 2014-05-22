<?php

namespace sys\utils;

use app\confs\Route as __route;

class Path
{
	
	use \sys\Loader;

	public function resolve($file)
	{
		return (($file = stream_resolve_include_path($file)) !== false) ? $file : false;
	}

	public function file($path, $base = app__, $ext = 'php')
	{
		return $this->load()->util('helper')->path($base . '/' . $path) . '.' . $ext;
	}

	public function root($path = '')
	{
		return ($path) ? $_SERVER['DOCUMENT_ROOT'] . '/' . $path : $_SERVER['DOCUMENT_ROOT'];
	}

	public function base($path = '')
	{
		return ($base = $this->load()->init('route')->path('base')) ? (($path) ? $base . '/' . $path : $base) : (($path) ? $path : '');
	}

	public function page($path = '')
	{
		return $this->load()->init('route')->controller() . '/' .
			 $this->load()->util('helper')->path(($path) ? $path : $this->load()->init('route')->page());
	}

	public function uri($path = '')
	{
		$path = ($path && $path != '/') ? '/' . $path : '';
		$base = (__route::rewrite__) ? (($base = $this->load()->init('route')->path('base')) ? '/' . $base : '') : '/' .
			 $this->load()->init('route')->path('file');
		return $base . $path;
	}

	public function request($path = '')
	{
		return $this->uri(
			$this->load()->init('route')->path('route') . '/' . \sys\modules\Request::param__ . '/' . $path);
	}

	public function trail($path = '')
	{
		return ($path) ? $path . __route::trail__ : '';
	}

}
