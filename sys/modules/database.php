<?php

namespace sys\modules;

class Database extends \sys\components\Database
{
	use \sys\traits\util\Helper;

	function __construct($dsn, $username, $password, $driver_options = [])
	{
		parent::__construct($dsn, $username, $password, $driver_options);
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
			if ($fetch_mode) {
				$this->sth->setFetchMode($fetch_mode);
			}
			return $this->sth->fetchAll();
		}
		return false;
	}

	public function update($table, $values = [], $clause = '', $params = [])
	{
		$values = $this->helper()->composeArray(' = ', $values);
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
