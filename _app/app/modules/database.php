<?php

namespace app\modules;

use app\configs\Database as __database;

class Database extends \sys\modules\Database
{

	function __construct($dsn = __database::dsn__, $username = __database::user__, $password = __database::pass__, $options = [])
	{
		parent::__construct($dsn, $username, $password, $options);
	}

}
