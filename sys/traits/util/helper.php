<?php

namespace sys\traits\util;

trait Helper
{

	private $_helper_util;

	protected function helper()
	{
		return (isset($this->_helper_util)) ? $this->_helper_util : $this->_helper_util = new \sys\utils\Helper();
	}

}
