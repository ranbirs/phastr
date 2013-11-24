<?php

namespace sys\traits;

trait Load {

	function __get($name)
	{
		return (isset($this->load()->$name)) ? $this->$name = $this->load()->$name : false;
	}

	public function load()
	{
		return \sys\Init::load();
	}

}
