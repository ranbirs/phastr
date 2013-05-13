<?php

namespace sys;

class Model {

	private static $dbh;

	function __construct()
	{

	}

	public function db()
	{
		if (!self::$dbh) {
			if (\app\confs\db\enabled__) {
				self::$dbh = new \sys\modules\Database();
			}
		}
		return self::$dbh;
	}

}
