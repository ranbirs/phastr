<?php

namespace sys\traits\module;

trait Hash
{

	private $_hash_module;

	protected function hash()
	{
		return (isset($this->_hash_module)) ? $this->_hash_module : $this->_hash_module = new \sys\modules\Hash();
	}

}
