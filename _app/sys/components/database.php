<?php

namespace sys\components;

use PDO;
use PDOException;

abstract class Database extends PDO
{

	public $sth;

	function __construct($dsn, $username, $password, $options = [])
	{
		if (!isset($options[PDO::ATTR_DEFAULT_FETCH_MODE])) {
			$options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_OBJ;
		}
		parent::__construct($dsn, $username, $password, $options);
	}

	public function query($statement, $values = null)
	{
		$this->sth = $this->prepare($statement);
		
		foreach ((array) $values as $key => $val) {
			call_user_func_array(array($this->sth, 'bindValue'), (array) ((is_int($key)) ? $key + 1 : $key) + (array) $val);
		}
		return $this->sth->execute();
	}

}