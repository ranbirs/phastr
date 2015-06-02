<?php

namespace app\modules;

use app\configs\Database as __database;

class Database extends \sys\modules\Database
{

	function __construct($dsn = __database::dsn__, $user = __database::user__, $pass = __database::pass__, $options = [])
	{
		parent::__construct($dsn, $user, $pass, $options);
	}

}
