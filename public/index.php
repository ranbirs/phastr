<?php
error_reporting(E_ALL);

define('dir__', '_app');

define('sys__', 'sys');
define('app__', 'app');

set_include_path((dir__) ? dirname(__DIR__) . '/' . dir__ : __DIR__);

require sys__ . '/_start.php';