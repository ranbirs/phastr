<?php

namespace sys\traits\util;

trait Path
{

	private $_path_util;

	protected function path()
	{
		return (isset($this->_path_util)) ? $this->_path_util : $this->_path_util = new \sys\utils\Path();
	}

}
