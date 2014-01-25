<?php

namespace sys\utils;

use sys\Util;

class Conf extends Util {

	public function k($const, $context = 'config', $base = app__)
	{
		if (is_null($constant = $this->constant($const .= '__', $context, $base))) {
			$this->loader()->resolveInclude($context, 'conf', false, app__);//
			$constant = $this->constant($const, $context, $base);
		}
		return $constant;
	}

	public function ini($path, $sections = true)
	{
		$path = get_include_path() . '/' . app__ . '/confs/' . $path . '.ini';
		return parse_ini_file($path, $sections);
	}

	protected function constant($const, $context, $base)
	{
		return constant($base . '\\confs\\' . $context . '\\' . $const);
	}

}
