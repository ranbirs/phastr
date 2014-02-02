<?php

namespace sys\utils;

class Loader extends \sys\Util
{

	public function resolveFile($path, $base = app__, $ext = 'php')
	{
		$path = $this->helper()->path($base . '/' . $path) . '.' . $ext;
		$file = stream_resolve_include_path($path);
		return ($file !== false) ? $path : false;
	}

}
