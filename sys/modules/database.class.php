<?php

namespace sys\modules;

use PDO;
use PDOException;
use sys\utils\Helper;

class Database extends PDO {

	const type__ = \app\confs\database\type__;
	const host__ = \app\confs\database\host__;
	const name__ = \app\confs\database\name__;
	const user__ = \app\confs\database\user__;
	const pass__ = \app\confs\database\pass__;

	protected $dsn, $sth;

	function __construct($dsn, $user, $pass)
	{
		try {
			parent::__construct($this->dsn = $dsn, $user, $pass);
		}
		catch (PDOException $e) {
			trigger_error($e->getMessage());
			exit;
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

	public function query($statement, $values = [])
	{
		$this->sth = $this->prepare($statement);

		if ($values === array_values($values)) {
			array_unshift($values, "");
			unset($values[0]);
		}
		foreach ($values as $key => $val) {

			if (!is_array($val))
				$val = [$val];

			switch (count($val)) {
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

	public function select($table, $cols = [], $clause = "", $params = [], $fetch = PDO::FETCH_OBJ)
	{
		$cols = implode(", ", $cols);
		$this->sth = $this->prepare("SELECT $cols FROM $table $clause");

		foreach ($params as $key => $val)
			$this->sth->bindValue($key, $val);
		$this->sth->execute();
		if ($this->sth->rowCount()) {
			return $this->sth->fetchAll($fetch);
		}
		return false;
	}

	public function update($table, $values = [], $clause = "", $params = [])
	{
		$values = Helper::getStringArray($values, " = ");
		$values = implode(", ", $values);
		$this->sth = $this->prepare("UPDATE $table SET $values $clause");

		foreach ($params as $key => $val)
			$this->sth->bindValue($key, $val);
		return $this->sth->execute();
	}

	public function insert($table, $values = [], $params = [])
	{
		$cols = implode(", ", array_keys($values));
		$values = implode(", ", array_values($values));
		$this->sth = $this->prepare("INSERT INTO $table ($cols) VALUES ($values)");

		foreach ($params as $key => $val)
			$this->sth->bindValue($key, $val);
		$this->sth->execute();
		return $this->lastInsertId();
	}

}
