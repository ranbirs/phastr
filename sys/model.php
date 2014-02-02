<?php

namespace sys;

abstract class Model
{
	
	use \sys\traits\Route;
	use \sys\traits\View;
	use \sys\traits\Load;
	use \sys\traits\Util;

	private $_database;

	function __construct()
	{
	}

	private function dsn($type = \app\confs\Database::type__, $host = \app\confs\Database::host__, $name = \app\confs\Database::name__)
	{
		return $type . ':host=' . $host . ';dbname=' . $name;
	}

	public function database($dsn = null, $user = \app\confs\Database::user__, $pass = \app\confs\Database::pass__)
	{
		return (isset($this->_database)) ? $this->_database : $this->_database = new \sys\modules\Database(
			($dsn) ? $dsn : $this->dsn(), $user, $pass);
	}

}
