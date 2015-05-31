<?php

namespace sys;

trait Loader
{

	private $_loader;

	protected function load($instance = null)
	{
		if (isset($instance)) {
			return new Load($instance);
		}
		return (isset($this->_loader)) ? $this->_loader : $this->_loader = new Load($this);
	}

}
