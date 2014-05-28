<?php

namespace sys;

trait Loader
{

	private $_loader, $_export;

	protected function load($instance = null)
	{
		if ($instance) {
			return $this->_export = (is_object($instance)) ? new \sys\Load($instance) : false;
		}
		return (isset($this->_loader)) ? $this->_loader : $this->_loader = new \sys\Load($this);
	}

}
