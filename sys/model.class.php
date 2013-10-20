<?php

namespace sys;

use sys\modules\Database;

abstract class Model {

	private static $dbh;

	function __construct()
	{

	}

	public function database($type = Database::type__, $host = Database::host__, $name = Database::name__, $user = Database::user__, $pass = Database::pass__)
	{
		if (!isset(self::$dbh)) {
			Init::load()->conf('database');
			self::$dbh = new Database($type . ":host=" . $host . ";dbname=" . $name, $user, $pass);
		}
		return self::$dbh;
	}

}
