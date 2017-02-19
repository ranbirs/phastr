<?php

error_reporting(E_ALL); // @todo env.

spl_autoload_register();

define('dir__', '_app');
define('sys__', 'sys');
define('app__', 'app');

set_include_path((dir__) ? dirname(__DIR__) . '/' . dir__ : __DIR__);

$_error = new \sys\handlers\Error();

set_error_handler([$_error, 'error']);
set_exception_handler([$_error, 'exception']);
