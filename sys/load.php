<?php

namespace sys;

class Load
{

	private $_instance;

	function __construct($_instance)
	{
		$this->_instance = $_instance;
	}

	private function _instance($path, $base = app__, $name = null)
	{
		$class = '\\' . $base . '\\' . implode('\\', explode('/', $path));
		return ($name) ? $this->_instance->$name = new $class() : new $class();
	}

	public function model($path)
	{
		return $this->_instance('models/' . $path, app__, basename($path));
	}

	public function module($path, $base = app__)
	{
		return $this->_instance('modules/' . $path, $base, basename($path));
	}

	public function form($path)
	{
		return $this->_instance('forms/' . $path, app__, basename($path));
	}

	public function nav($path)
	{
		return $this->_instance('navs/' . $path, app__, basename($path));
	}

	public function service($path, $data = null)
	{
		return $this->_instance('services/' . $path, app__, basename($path));
	}

	public function init($path)
	{
		return $this->_instance('controllers/' . $path, app__);
	}

}
