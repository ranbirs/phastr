<?php

namespace sys\traits\module;

use app\confs\Database as __Database;

trait Database
{

	private $_database_module;

	protected function dsn($driver = __Database::type__, $host = __Database::host__, $name = __Database::name__)
	{
		return $driver . ':host=' . $host . ';dbname=' . $name;
	}

	public function database($dsn = null, $username = __Database::user__, $password = __Database::pass__, $driver_options = [])
	{
		return (isset($this->_database_module)) ? $this->_database_module : $this->_database_module = new \sys\modules\Database(
			(!$dsn) ? $this->dsn() : $dsn, $username, $password, $driver_options);
	}

}