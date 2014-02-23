<?php

namespace sys\modules;

use PDO;
use PDOException;

class Database extends PDO
{
	use \sys\traits\util\Helper;

	protected $sth, $client;

	function __construct($dsn, $user, $pass)
	{
		try {
			parent::__construct($dsn, $user, $pass);
		}
		catch (PDOException $e) {
			throw $e;
			exit();
		}
	}

	public function sth()
	{
		return $this->sth;
	}

	public function client()
	{
		return $this->client = $this;
	}

	public function query($statement, $values = [])
	{
		$this->sth = $this->prepare($statement);
		
		if ($values === array_values($values)) {
			array_unshift($values, null);
			unset($values[0]);
		}
		foreach ($values as $key => $val) {
			switch (count($val = (array) $val)) {
				case 2:
					$this->sth->bindValue($key, $val[0], $val[1]);
					break;
				case 1:
					$this->sth->bindValue($key, $val[0]);
					break;
				default:
					return false;
			}
		}
		$this->sth->execute();
		return $this->sth;
	}

	public function select($table, $cols = [], $clause = '', $params = [], $fetch = PDO::FETCH_OBJ)
	{
		$cols = implode(', ', $cols);
		$this->sth = $this->prepare("SELECT $cols FROM $table $clause");
		
		foreach ($params as $key => $val) {
			$this->sth->bindValue($key, $val);
		}
		$this->sth->execute();
		if ($this->sth->rowCount()) {
			return $this->sth->fetchAll($fetch);
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
	
	function __destruct()
	{
		$this->sth = null;
		$this->client = null;
	}

}
