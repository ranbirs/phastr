<?php

namespace sys\traits;

trait Rest {

	private $_rest;

	protected function rest($new = false)
	{
		if (!isset($this->_rest) or $new)
			$this->_rest = new \sys\modules\Rest();
		return $this->_rest;
	}

}
