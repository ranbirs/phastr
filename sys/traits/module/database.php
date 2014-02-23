<?php

namespace sys\traits\module;

use app\confs\Database as DatabaseConf;

trait Database
{

	private $_database;

	protected function dsn($type = DatabaseConf::type__, $host = DatabaseConf::host__, $name = DatabaseConf::name__)
	{
		return $type . ':host=' . $host . ';dbname=' . $name;
	}

	public function database($dsn = null, $user = DatabaseConf::user__, $pass = DatabaseConf::pass__)
	{
		return (isset($this->_database)) ? $this->_database : $this->_database = new \sys\modules\Database(
			($dsn) ? $dsn : $this->dsn(), $user, $pass);
	}

}