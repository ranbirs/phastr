<?php

namespace sys\traits;

use sys\Init;
use sys\utils\Helper;

trait Loader {

	protected function load($type, $path, $args = null)
	{
		$subj = Helper::getPathName($path);
		return $this->$subj = Init::load()->get($type, $path, $args);
	}

}
