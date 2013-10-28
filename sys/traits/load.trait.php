<?php

namespace sys\traits;

use sys\Init;

trait Load {

	function __get($name)
	{
		return $this->$name = (isset($this->load()->$name)) ? $this->load()->$name : null;
	}

	protected function load()
	{
		return Init::load();
	}

}
