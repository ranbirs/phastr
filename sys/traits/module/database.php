<?php

namespace sys\traits\module;

use app\confs\Database as __database;

trait Database
{

	private $_database_module;

	protected function dsn($driver = __database::type__, $host = __database::host__, $name = __database::name__)
	{
		return $driver . ':host=' . $host . ';dbname=' . $name;
	}

	public function database($dsn = null, $username = __database::user__, $password = __database::pass__, $driver_options = [])
	{
		return (isset($this->_database_module)) ? $this->_database_module : $this->_database_module = new \sys\modules\Database(
			(!$dsn) ? $this->dsn() : $dsn, $username, $password, $driver_options);
	}

}