<?php

namespace sys\modules;

use sys\utils\Helper;

class Database extends \sys\components\Database
{

	function __construct($driver, $host, $name, $username, $password, $options = [])
	{
		parent::__construct($driver . ':host=' . $host . ';dbname=' . $name, $username, $password, $options);
	}

	public function select($table, $cols, $clause = null, $params = [], $fetch_mode = null)
	{
		$cols = implode(', ', (array) $cols);
		$this->sth = $this->prepare("SELECT $cols FROM $table $clause");
		
		foreach ($params as $key => $val) {
			$this->sth->bindValue($key, $val);
		}
		$this->sth->execute();
		if ($this->sth->rowCount()) {
			if (isset($fetch_mode)) {
				$this->sth->setFetchMode($fetch_mode);
			}
			return $this->sth->fetchAll();
		}
		return false;
	}

	public function update($table, $values = [], $clause = null, $params = [])
	{
		$values = Helper::iterate_join($values, ' = ');
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
