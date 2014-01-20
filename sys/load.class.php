<?php

namespace sys;

class Load {

	use \sys\traits\Util;
	use \sys\traits\Instance;

	function __construct()
	{

	}

	public function model($path)
	{
		return $this->util()->loader()->resolveInclude($path, 'model', true, $this->instance(), app__, 'model.php');
	}

	public function service($path, $data = null)
	{
		return $this->util()->loader()->resolveInclude($path, 'service', true, $this->instance(), app__, 'service.php');
	}

	public function form($path)
	{
		return $this->util()->loader()->resolveInclude($path, 'form', true, $this->instance(), app__, 'form.php');
	}

	public function nav($path)
	{
		return $this->util()->loader()->resolveInclude($path, 'nav', true, $this->instance(), app__, 'nav.php');
	}

	public function module($path, $base = app__)
	{
		$ext = ($base == app__) ? 'module.php' : 'class.php';
		return $this->util()->loader()->resolveInclude($path, 'module', true, $this->instance(), $base, $ext);
	}

	public function controller($path)
	{
		return $this->util()->loader()->resolveInclude($path, 'controller', true, false, app__, 'controller.php');
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
