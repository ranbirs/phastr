<?php

namespace sys\components;

use PDO;
use PDOException;

class Database extends PDO
{

	protected $sth, $client;

	function __construct($dsn, $username, $password, $driver_options = [])
	{
		try {
			if (!isset($driver_options[PDO::ATTR_DEFAULT_FETCH_MODE])) {
				$driver_options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_OBJ;
			}
			parent::__construct($dsn, $username, $password, $driver_options);
		}
		catch (PDOException $e) {
			throw $e;
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

	public function query($statement, $values = null)
	{
		$this->sth = $this->prepare($statement);

		foreach ($values as $key => $val) {
			call_user_func_array(array($this->sth, 'bindValue'), ((array) (is_int($key)) ? $key + 1 : $key) + ((array) $val));
		}
		$this->sth->execute();
		
		return $this->sth;
	}

}
