<?php

namespace sys\traits;

trait Load
{

	public function load()
	{
		return \sys\Init::load()->instance($this);
	}

}
