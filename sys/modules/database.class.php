<?php

namespace sys\modules;

use PDO;
use PDOException;

class Database extends PDO
{
	use \sys\traits\Util;
	const type__ = \app\confs\Database::type__;
	const host__ = \app\confs\Database::host__;
	const name__ = \app\confs\Database::name__;
	const user__ = \app\confs\Database::user__;
	const pass__ = \app\confs\Database::pass__;

	protected $dsn, $sth, $client;

	function __construct($dsn, $user, $pass)
	{
		try {
			parent::__construct($this->dsn = $dsn, $user, $pass);
		}
		catch (PDOException $e) {
			throw $e;
			exit();
		}
	}

	public function dsn()
	{
		return $this->dsn;
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
		
		if ($this->util()->helper()->isIndexArray($values)) {
			$values = $this->util()->helper()->shiftArrayIndex($values, 1);
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
		return $this->sth->execute();
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
		$values = $this->util()->helper()->composeArray(' = ', $values);
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
