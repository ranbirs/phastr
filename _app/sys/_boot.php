<?php

set_include_path(dirname(__DIR__));

spl_autoload_register();

$_error = new \sys\handlers\Error();

set_error_handler([$_error, 'error']);
set_exception_handler([$_error, 'exception']);