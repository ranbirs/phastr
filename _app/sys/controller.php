<?php

namespace sys;

abstract class Controller
{
	
	use \sys\Loader;

	abstract public function init();
	
	abstract public function render();

}
