<?php

namespace sys\traits;

trait Load
{

	private $_load_trait;

	protected function load()
	{
		return (isset($this->_load_trait)) ? $this->_load_trait : $this->_load_trait = new \sys\Load($this);
	}

}
