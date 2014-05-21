<?php

namespace sys\traits;

trait View
{

	private $_view_trait;

	protected function view()
	{
		return (isset($this->_view_trait)) ? $this->_view_trait : $this->_view_trait = \sys\Init::view();
	}

}
