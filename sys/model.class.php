<?php

namespace sys;

abstract class Model {

	private static $dbh;

	function __construct()
	{

	}

	public function database()
	{

		if (!isset(self::$dbh)) {
			Init::load()->conf('database');
			self::$dbh = new \sys\modules\Database();
		}
		return self::$dbh;
	}

}
