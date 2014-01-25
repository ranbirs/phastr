<?php

namespace sys;

use sys\modules\Database;

abstract class Model {

	use \sys\traits\Route;//
	use \sys\traits\Util;
	use \sys\traits\Load;
	use \sys\traits\View;//
	use \sys\traits\Session;//
	use \sys\traits\Request;//

	private $_database;

	function __construct()
	{

	}

	public function database($type = Database::type__, $host = Database::host__, $name = Database::name__, $user = Database::user__, $pass = Database::pass__)
	{
		if (!isset($this->_database))
			$this->_database = new Database($type . ':host=' . $host . ';dbname=' . $name, $user, $pass);
		return $this->_database;
	}

}
