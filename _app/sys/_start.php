<?php

spl_autoload_register();

$_error = new \sys\handlers\Error();

set_error_handler([$_error, 'error']);
set_exception_handler([$_error, 'exception']);

$_init = new \app\init\Mvc();

exit();