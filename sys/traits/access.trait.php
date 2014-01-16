<?php

namespace sys\traits;

trait Access {

	private $_access;

	public function access()
	{
		if (!isset($this->_access))
			$this->_access = new \sys\modules\Access;
		return $this->_access;
	}

}
