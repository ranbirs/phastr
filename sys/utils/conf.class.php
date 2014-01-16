<?php

namespace sys\utils;

class Conf extends \sys\Utils {

	public function k($const, $context = 'config', $base = app__)
	{
		if (is_null($constant = $this->getConst($const .= '__', $context, $base))) {
			\sys\Init::load()->conf($context, $base);
			$constant = $this->getConst($const, $context, $base);
		}
		return $constant;
	}

	public function ini($path, $sections = true)
	{
		$path = get_include_path() . '/' . \sys\base_path('confs/' . $path) . '.ini';
		return parse_ini_file($path, $sections);
	}

	public function getConst($const, $context, $base)
	{
		return constant($base . '\\confs\\' . $context . '\\' . $const);
	}

}
