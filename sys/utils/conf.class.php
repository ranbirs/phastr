<?php

namespace sys\utils;

use sys\Util;

class Conf extends Util
{

	public function k($const, $context = 'config', $base = app__)
	{
		return $this->constant($const .= '__', $context, $base);
	}

	public function ini($path, $sections = true)
	{
		$path = get_include_path() . '/' . app__ . '/confs/' . $path . '.ini';
		return parse_ini_file($path, $sections);
	}

	protected function constant($const, $context, $base)
	{
		return constant($base . '\\confs\\' . $context . '::' . $const);
	}

}
