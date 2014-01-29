<?php

namespace sys;

class Load
{
	use \sys\traits\Util;
	use \sys\traits\Instance;

	function __construct()
	{
	}

	public function module($path, $base = app__)
	{
		$ext = [app__ => 'php',sys__ => 'class.php'];
		return $this->util()->loader()->resolveInclude($path, 'module', true, $this->instance(), $base, $ext[$base]);
	}

	public function model($path)
	{
		return $this->util()->loader()->resolveInclude($path, 'model', true, $this->instance());
	}

	public function form($path)
	{
		return $this->util()->loader()->resolveInclude($path, 'form', true, $this->instance());
	}

	public function nav($path)
	{
		return $this->util()->loader()->resolveInclude($path, 'nav', true, $this->instance());
	}

	public function service($path, $data = null)
	{
		return $this->util()->loader()->resolveInclude($path, 'service', true, $this->instance());
	}

	public function controller($path)
	{
		return $this->util()->loader()->resolveInclude($path, 'controller', true, false);
	}

	public function conf($path, $base = app__)
	{
		return $this->util()->loader()->resolveInclude($path, 'conf', false, $base);
	}

	public function vocab($path, $lang = '')
	{
		return $this->util()->loader()->resolveInclude(($lang) ? $lang . '/' . $path : $path, 'vocab', false);
	}

}
