<?php

namespace sys;

class Load
{

	private $_instance;

	function __construct($instance)
	{
		$this->_instance = $instance;
	}

	protected function composite($instance, $path, $base = app__)
	{
		$class = '\\' . $base . '\\' . implode('\\', explode('/', $path));
		return $instance->{basename($path)} = new $class();
	}

	public function module($path, $base = sys__)
	{
		return $this->composite($this->_instance, 'modules/' . $path, $base);
	}

	public function model($path)
	{
		return $this->composite($this->_instance, 'models/' . $path);
	}

	public function form($path)
	{
		return $this->composite($this->_instance, 'forms/' . $path);
	}

	public function nav($path)
	{
		return $this->composite($this->_instance, 'navs/' . $path);
	}

	public function service($path)
	{
		return $this->composite($this->_instance, 'services/' . $path);
	}

}
