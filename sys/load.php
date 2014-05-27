<?php

namespace sys;

class Load
{

	private $_instance;

	function __construct($instance)
	{
		$this->_instance = $instance;
	}

	protected function getInstance($path, $base = app__, $prop = true)
	{
		if (!isset($this->_instance->{$name = basename($path)}) || !$prop) {
			$class = '\\' . $base . '\\' . implode('\\', explode('/', $path));
			$instance = new $class();
			return (!$prop) ? $instance : $this->_instance->{$name} = $instance;
		}
		return $this->_instance->{$name};
	}

	public function init($subj, $prop = true)
	{
		if (!isset($this->_instance->{$subj}) || !$prop) {
			$instance = call_user_func('\\' . sys__ . '\\Init::' . $subj);
			return (!$prop) ? $instance : $this->_instance->{$subj} = $instance;
		}
		return $this->_instance->{$subj};
	}

	public function module($path, $base = sys__, $prop = true)
	{
		return $this->getInstance('modules/' . $path, $base, $prop);
	}

	public function model($path, $prop = true)
	{
		return $this->getInstance('models/' . $path, app__, $prop);
	}

	public function form($path, $prop = true)
	{
		return $this->getInstance('forms/' . $path, app__, $prop);
	}

	public function nav($path, $prop = true)
	{
		return $this->getInstance('navs/' . $path, app__, $prop);
	}

	public function service($path, $prop = true)
	{
		return $this->getInstance('services/' . $path, app__, $prop);
	}

}
