<?php

namespace sys\traits;

trait Route
{

	private $_init_route;

	protected function route()
	{
		return (isset($this->_init_route)) ? $this->_init_route : $this->_init_route = \sys\Init::route();
	}

}
