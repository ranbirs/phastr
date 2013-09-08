<?php

define('sys__', "sys");
define('app__', "app");
define('pub__', basename(__DIR__));
define('app_srv__', 'SERVER_NAME');

set_include_path(dirname(__DIR__));

require sys__ . "/_start.php";
