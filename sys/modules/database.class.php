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

	public function query($statement, $values = [])
	{
		$q = $this->prepare($statement);

		foreach ($values as $key => $val) {

			if (!is_array($val)) {
				$val = [$val];
			}
			switch (count($val)) {
				case 2:
					$q->bindValue(":" . $key, $val[0], $val[1]);
					break;
				case 1:
					$q->bindValue(":" . $key, $val[0]);
					break;
				default:
					return false;
			}
		}
		$q->execute();
		return $q;
	}

	public function select($table, $cols = [], $clause = null, $values = [], $fetch = PDO::FETCH_OBJ)
	{
		$cols = implode(", ", $cols);
		$q = $this->prepare("SELECT $cols FROM $table $clause");

		foreach ($values as $key => $val)
			$q->bindValue(":" . $key, $val);
		$q->execute();
		if ($q->rowCount()) {
			return $q->fetchAll($fetch);
		}
		return false;
	}

	public function update($table, $values = [], $clause = null, $params = [])
	{
		$keys = array_keys($values);
		$args = [];
		foreach($keys as $key)
			$args[] = $key . "= :" . $key;
		$args = implode(", ", $args);
		$q = $this->prepare("UPDATE $table SET $args $clause");

		foreach (array_merge($values, $params) as $key => $val)
			$q->bindValue(":" . $key, $val);
		return $q->execute();
	}

	public function insert($table, $values = [])
	{
		$keys = array_keys($values);
		$cols = implode(", ", $keys);
		$args = ":" . implode(", :", $keys);
		$q = $this->prepare("INSERT INTO $table ($cols) VALUES ($args)");

		foreach ($values as $key => $val)
			$q->bindValue(":" . $key, $val);
		$q->execute();
		return $this->lastInsertId();
	}

}
