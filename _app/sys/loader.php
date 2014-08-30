<?php

namespace sys;

trait Loader
{

	private $_loader, $_import;

	protected function load($instance = null)
	{
		if (isset($instance)) {
			return $this->_import = new \sys\Load($instance);
		}
		return (isset($this->_loader)) ? $this->_loader : $this->_loader = new \sys\Load($this);
	}

}
