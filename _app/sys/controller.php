<?php

namespace sys;

abstract class Controller
{
	
	use \sys\Loader;

	public function init()
	{
	}
	
	public function render()
	{
	    exit();
	}

}
