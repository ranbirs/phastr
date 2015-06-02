<?php

namespace sys;

trait Loader
{

	private $_loader;

	protected function loader($instance = null)
	{
		if (isset($instance)) {
			return (is_object($instance)) ? new Load($instance) : false;
		}
		return (isset($this->_loader)) ? $this->_loader : $this->_loader = new Load($this);
	}

}
