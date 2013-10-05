<?php

namespace sys;

class Load {

	use \sys\traits\Loader;

	function __construct()
	{

	}

	public function get($type, $path, $args = null)
	{
		return (method_exists($this, $type)) ? $this->$type($path, $args) : false;
	}

	public function model($path)
	{
		return $this->resolveInclude($path, 'model');
	}

	public function service($path, $data = null)
	{
		return $this->resolveInclude($path, 'service');
	}

	public function form($path)
	{
		return $this->resolveInclude($path, 'form');
	}

	public function nav($path)
	{
		return $this->resolveInclude($path, 'nav');
	}

	public function controller($path)
	{
		return $this->resolveInclude($path, 'controller');
	}

	public function conf($path, $base = app__)
	{
		return $this->resolveInclude($path, 'conf', false, $base);
	}

	public function vocab($path, $lang = "")
	{
		return $this->resolveInclude(($lang) ? $lang . "/" . $path : $path, 'vocab', false);
	}

}
