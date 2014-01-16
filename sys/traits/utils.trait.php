<?php

namespace sys\traits;

trait Utils {

	private $_utils;

	public function utils()
	{
		if (!isset($this->_utils))
			$this->_utils = new \sys\Utils;
		return $this->_utils;
	}

}
