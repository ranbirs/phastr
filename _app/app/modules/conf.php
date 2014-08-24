<?php

namespace app\modules;

class Conf
{

	public function k($const, $context)
	{
		return $this->constant($const .= '__', $context);
	}

	public function ini($path, $sections = true)
	{
		$path = get_include_path() . '/' . app__ .'/' . $path . '.ini';
		return parse_ini_file($path, $sections);
	}

	protected function constant($const, $context)
	{
		return constant('\\app\\confs\\' . $context . '::' . $const);
	}

}
