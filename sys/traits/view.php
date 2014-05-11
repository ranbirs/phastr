<?php

namespace sys\traits;

trait View
{

	private $_init_view;

	protected function view()
	{
		return (isset($this->_init_view)) ? $this->_init_view : $this->_init_view = \sys\Init::view();
	}

}
