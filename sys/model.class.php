<?php

namespace sys;

use sys\modules\Database;

class Model {

	private static $dbh;

	function __construct()
	{

	}

	public function db()
	{
		if (!\app\confs\db\enabled__) {
			return false;
		}
		if (!isset(self::$dbh))
			self::$dbh = new Database();
		return self::$dbh;
	}

}
