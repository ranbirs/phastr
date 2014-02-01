<?php

namespace sys\traits;

trait Util
{

	private $_util;

	public function util()
	{
		return (isset($this->_util)) ? $this->_util : new \sys\Util();
	}

}
