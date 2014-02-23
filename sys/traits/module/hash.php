<?php

namespace sys\traits\module;

trait Hash
{

	private $_hash;

	protected function hash()
	{
		return (isset($this->_hash)) ? $this->_hash : $this->_hash = new \sys\modules\Hash();
	}

}
