<?php

namespace sys\components;

use sys\components\Loader;

class Load extends Loader {

	function __construct()
	{

	}

	public function model($path)
	{
		return self::resolveInclude("models/$path", 'composite');
	}

	public function form($path)
	{
		return self::resolveInclude("forms/$path", 'composite');
	}

	public function nav($path)
	{
		return self::resolveInclude("navs/$path", 'composite');
	}

}
