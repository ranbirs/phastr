<?php
error_reporting(E_ALL); /* Set the error reporting level */

define('dir__', '_app'); /* Set the app and system folder or empty if they must be public */

define('sys__', 'sys');
define('app__', 'app');

define('eol__', PHP_EOL);

set_include_path((dir__) ? dirname(__DIR__) . '/' . dir__ : __DIR__);

require sys__ . '/_start.php';