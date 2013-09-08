<?php

namespace sys;

use \sys\utils\Loader;

class Load {

	function __construct()
	{

	}

	public function get($type, $path, $args = null)
	{
		return (method_exists($this, $type)) ? $this->$type($path, $args) : false;
	}

	public function model($path)
	{
		return Loader::resolveInclude($path, 'model');
	}

	public function service($path, $data = null)
	{
		return Loader::resolveInclude($path, 'service');
	}

	public function form($path)
	{
		return Loader::resolveInclude($path, 'form');
	}

	public function nav($path)
	{
		return Loader::resolveInclude($path, 'nav');
	}

	public function controller($path)
	{
		return Loader::resolveInclude($path, 'controller');
	}

	public function conf($path, $base = app__)
	{
		return Loader::resolveInclude($path, 'conf', false, $base);
	}

	public function vocab($path, $lang = "")
	{
		return Loader::resolveInclude(($lang) ? $lang . "/" . $path : $path, 'vocab', false);
	}

}
