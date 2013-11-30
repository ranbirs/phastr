<?php

//define('dir__', DIRECTORY_SEPARATOR);
define('eol__', PHP_EOL);

define('sys__', 'sys');
define('app__', 'app');
define('public__', basename(__DIR__));
define('app_server__', 'SERVER_NAME');

set_include_path(dirname(__DIR__));

require sys__ . '/_start.php';
