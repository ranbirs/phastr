<?php

namespace sys\modules;

use PDO;
use PDOException;

class Database extends PDO {

	function __construct()
	{
		try {
			$dsn = \app\confs\db\type__ . ":host=" . \app\confs\db\host__ . ";dbname=" . \app\confs\db\name__;
			parent::__construct($dsn, \app\confs\db\user__, \app\confs\db\pass__);
		}
		catch(PDOException $e) {
			trigger_error($e->getMessage());
			exit();
		}
	}

	public function select($table, $fields = array(), $data = array(), $fetch = PDO::FETCH_OBJ)
	{
		$clause = $data[0];
		$values = $data[1];
		$columns = implode(", ", $fields);

		$q = $this->prepare("SELECT $columns FROM $table $clause");
		foreach ($values as $key => $val) {
			$q->bindValue(":$key", $val);
		}
		$q->execute();
		if ($q->rowCount()) {
			return $q->fetchAll($fetch);
		}
		return false;
	}

	public function update($table, $data = array(), $clause = array())
	{
		$values = $clause[1];
		$clause = $clause[0];
		$fields = array_keys($data);
		$columns = array();
		foreach($fields as $field) {
			$columns[] = "$field = :$field";
		}
		$columns = implode(", ", $columns);

		$q = $this->prepare("UPDATE $table SET $columns $clause");
		foreach (array_merge($data, $values) as $key => $val) {
			$q->bindValue(":$key", $val);
		}
		return $q->execute();
	}

	public function insert($table, $data = array())
	{
		$fields = array_keys($data);
		$columns = implode(", ", $fields);
		$values = ":" . implode(", :", $fields);

		$q = $this->prepare("INSERT INTO $table ($columns) VALUES ($values)");
		foreach ($data as $key => $val) {
			$q->bindValue(":$key", $val);
		}
		$q->execute();

		return $this->lastInsertId();
	}

}
