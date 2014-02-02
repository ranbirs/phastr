<?php

namespace sys\traits;

trait Load
{

	private $_load;

	public function load()
	{
		return (isset($this->_load)) ? $this->_load : new \sys\Load($this);
	}

}
