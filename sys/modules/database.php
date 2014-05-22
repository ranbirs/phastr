<?php

namespace sys\modules;

use app\confs\Database as __database;

class Database extends \sys\components\Database
{
	
	use \sys\Loader;

	function __construct($dsn = null, $username = __database::user__, $password = __database::pass__, $driver_options = [])
	{
		parent::__construct((is_null($dsn)) ? $this->dsn() : $dsn, $username, $password, $driver_options);
	}

	protected function dsn($driver = __database::type__, $host = __database::host__, $name = __database::name__)
	{
		return $driver . ':host=' . $host . ';dbname=' . $name;
	}

	public function select($table, $cols = [], $clause = '', $params = [], $fetch_mode = null)
	{
		$cols = implode(', ', $cols);
		$this->sth = $this->prepare("SELECT $cols FROM $table $clause");
		
		foreach ($params as $key => $val) {
			$this->sth->bindValue($key, $val);
		}
		$this->sth->execute();
		if ($this->sth->rowCount()) {
			if (!is_null($fetch_mode)) {
				$this->sth->setFetchMode($fetch_mode);
			}
			return $this->sth->fetchAll();
		}
		return false;
	}

	public function update($table, $values = [], $clause = '', $params = [])
	{
		$values = $this->load()->util('helper')->composeArray(' = ', $values);
		$values = implode(', ', $values);
		$this->sth = $this->prepare("UPDATE $table SET $values $clause");
		
		foreach ($params as $key => $val) {
			$this->sth->bindValue($key, $val);
		}
		return $this->sth->execute();
	}

	public function insert($table, $values = [], $params = [])
	{
		$cols = implode(', ', array_keys($values));
		$values = implode(', ', array_values($values));
		$this->sth = $this->prepare("INSERT INTO $table ($cols) VALUES ($values)");
		
		foreach ($params as $key => $val) {
			$this->sth->bindValue($key, $val);
		}
		$this->sth->execute();
		return $this->lastInsertId();
	}

}
