<?php

namespace sys\modules;

use PDO;
use PDOException;

class Database extends PDO {

	function __construct()
	{
		try {
			$dsn = \app\confs\database\type__ . ":host=" . \app\confs\database\host__ . ";dbname=" . \app\confs\database\name__;
			parent::__construct($dsn, \app\confs\database\user__, \app\confs\database\pass__);
		}
		catch (PDOException $e) {
			trigger_error($e->getMessage());
			exit;
		}
	}

	public function query($statement, $values = array())
	{
		$q = $this->prepare($statement);

		foreach ($values as $key => $val) {
			if (is_int($key) and is_array($val)) {
				switch (count($val)) {
					case 3:
						$q->bindValue(":" . $val[0], $val[1], $val[2]);
						break;
					case 2:
						$q->bindValue(":" . $val[0], $val[1]);
						break;
					default:
						return false;
				}
			}
			else {
				$q->bindValue(":$key", $val);
			}
		}
		$q->execute();
		return $q;
	}

	public function select($table, $columns = array(), $clause = null, $values = array(), $fetch = PDO::FETCH_OBJ)
	{
		$columns = implode(", ", $columns);
		$q = $this->prepare("SELECT $columns FROM $table $clause");

		foreach ($values as $key => $val)
			$q->bindValue(":$key", $val);
		$q->execute();
		if ($q->rowCount()) {
			return $q->fetchAll($fetch);
		}
		return false;
	}

	public function update($table, $values = array(), $clause = null, $args = array())
	{
		$keys = array_keys($values);
		$binds = array();
		foreach($keys as $key)
			$binds[] = "$key = :$key";
		$binds = implode(", ", $binds);
		$q = $this->prepare("UPDATE $table SET $binds $clause");

		foreach (array_merge($values, $args) as $key => $val)
			$q->bindValue(":$key", $val);
		return $q->execute();
	}

	public function insert($table, $values = array())
	{
		$keys = array_keys($values);
		$columns = implode(", ", $keys);
		$binds = ":" . implode(", :", $keys);
		$q = $this->prepare("INSERT INTO $table ($columns) VALUES ($binds)");

		foreach ($values as $key => $val)
			$q->bindValue(":$key", $val);
		$q->execute();
		return $this->lastInsertId();
	}

}
