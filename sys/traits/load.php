<?php

namespace sys\traits;

trait Load
{

	private $_sys_load;

	protected function load()
	{
		return (isset($this->_sys_load)) ? $this->_sys_load : $this->_sys_load = new \sys\Load($this);
	}

}
