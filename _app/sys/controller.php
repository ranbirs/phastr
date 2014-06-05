<?php

namespace sys;

abstract class Controller
{
	
	use \sys\Loader;

	function __construct()
	{
		$this->load()->init('route');
		$this->load()->init('view');
	}

	public function init($method, $params = [])
	{
		if (method_exists($this, $method)) {
			$this->{$method}($params);
			
			return true;
		}
		return false;
	}

	public function render()
	{
		$this->load()->module('session')->render();
		$this->view->layout();
	}

}
