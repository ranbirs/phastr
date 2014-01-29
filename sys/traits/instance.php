<?php

namespace sys\traits;

trait Instance
{

	private $_instance;

	public function instance($instance = null)
	{
		if (is_null($instance)) {
			return (isset($this->_instance)) ? $this->_instance : false;
		}
		$this->_instance = $instance;
		return $this;
	}

}