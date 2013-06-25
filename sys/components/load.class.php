<?php

namespace sys\components;

use sys\components\Loader;

class Load extends Loader {

	function __construct()
	{

	}

	public function model($path)
	{
		return self::resolveInclude($path, 'model', 'composite');
	}

	public function form($path)
	{
		return self::resolveInclude($path, 'form', 'composite');
	}

	public function nav($path)
	{
		return self::resolveInclude($path, 'nav', 'composite');
	}

}
