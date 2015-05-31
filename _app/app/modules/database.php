<?php

namespace app\modules;

use app\configs\Database as __database;

class Database extends \sys\modules\Database
{

    function __construct($type = __database::type__, $host = __database::host__, $name = __database::name__, $user = __database::user__, $pass = __database::pass__, $options = [])
    {
        parent::__construct($type . ':host=' . $host . ';dbname=' . $name, $user, $pass, $options);
    }

}
