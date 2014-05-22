<?php

namespace sys;

trait Loader
{

	private $_loader, $_export;

	protected function load($instance = null)
	{
		if (!is_null($instance) && is_object($instance)) {
			return $this->_export = new \sys\Load($instance);
		}
		return (isset($this->_loader)) ? $this->_loader : $this->_loader = new \sys\Load($this);
	}

}
