<?php

namespace sys;

abstract class Model {

	private static $dbh;

	function __construct()
	{
		Load::conf('database');
	}

	public function database()
	{
		if (!\app\confs\database\enabled__) {
			return false;
		}
		if (!isset(self::$dbh))
			self::$dbh = new \sys\modules\Database();
		return self::$dbh;
	}

}
