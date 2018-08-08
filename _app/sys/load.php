<?php

namespace sys;

use ReflectionClass;
use ReflectionException;

class Load
{

	private $_instance;

	function __construct($instance)
	{
		$this->_instance = &$instance;
	}

	public function load($path, $prop = null, $args = null)
	{
		if (!isset($prop)) {
			$prop = basename($path);
		}
		$class = '\\' . str_replace('/', '\\', $path);
		return $this->_instance->{$prop} = $this->reflect($class, $args);
	}

	public function init($path, $prop = null, $args = null)
	{
		if (!isset($prop)) {
			$prop = basename($path);
		}
		if (isset(Init::$init->{$prop})) {
			return $this->_instance->{$prop} = Init::$init->{$prop};
		}
		$class = '\\' . str_replace('/', '\\', $path);
		return $this->_instance->{$prop} = Init::$init->{$prop} = $this->reflect($class, $args);
	}

	protected function reflect($class, $args = null)
	{
		return (!isset($args)) ? new $class() : (new ReflectionClass($class))->newInstanceArgs((array) $args);
	}

}
