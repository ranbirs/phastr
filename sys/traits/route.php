<?php

namespace sys\traits;

trait Route
{

	private $_route_trait;

	protected function route()
	{
		return (isset($this->_route_trait)) ? $this->_route_trait : $this->_route_trait = \sys\Init::route();
	}

}
