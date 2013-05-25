<?php

namespace sys;

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
			self::$dbh = new \sys\modules\Database();

		return self::$dbh;
	}

}
