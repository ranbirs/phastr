<?php

namespace sys;

use sys\modules\Database;

abstract class Model {

	protected $dbh;

	function __construct()
	{

	}

	public function database($type = Database::type__, $host = Database::host__, $name = Database::name__, $user = Database::user__, $pass = Database::pass__)
	{
		if (!isset($this->dbh))
			$this->dbh = new Database($type . ":host=" . $host . ";dbname=" . $name, $user, $pass);
		return $this->dbh;
	}

}
