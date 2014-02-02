<?php

namespace sys;

class Load
{
	
	use \sys\traits\Util;

	private $_instance;

	function __construct($instance)
	{
		$this->_instance = $instance;
	}

	private function _instance($path, $type, $base = app__, $prop = true)
	{
		$path = $this->util()->helper()->path($type . 's/' . $path);
		$name = $this->util()->helper()->pathName($path);
		$class = $this->util()->helper()->pathClass('\\' . $base . '\\' . $path);
		return ($prop) ? $this->_instance->$name = new $class() : new $class();
	}

	public function model($path)
	{
		return $this->_instance($path, 'model');
	}

	public function module($path, $base = app__)
	{
		return $this->_instance($path, 'module', $base);
	}

	public function form($path)
	{
		return $this->_instance($path, 'form');
	}

	public function nav($path)
	{
		return $this->_instance($path, 'nav');
	}

	public function service($path, $data = null)
	{
		return $this->_instance($path, 'service');
	}

	public function init($path)
	{
		return $this->_instance($path, 'controller', app__, false);
	}

}
