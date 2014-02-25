<?php

namespace sys\traits\module;

use app\confs\Database as DatabaseConf;

trait Database
{

	private $_database_module;

	protected function dsn($driver = DatabaseConf::type__, $host = DatabaseConf::host__, $name = DatabaseConf::name__)
	{
		return $driver . ':host=' . $host . ';dbname=' . $name;
	}

	public function database($dsn = null, $username = DatabaseConf::user__, $password = DatabaseConf::pass__, $driver_options = [])
	{
		return (isset($this->_database_module)) ? $this->_database_module : $this->_database_module = new \sys\modules\Database(
			(!$dsn) ? $this->dsn() : $dsn, $username, $password, $driver_options);
	}

}