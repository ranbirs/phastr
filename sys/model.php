<?php

namespace sys;

abstract class Model
{
	
	use \sys\traits\Route;
	use \sys\traits\View;
	use \sys\traits\Load;
	use \sys\traits\module\Database;

	function __construct()
	{
	}

}
