<?php
spl_autoload_register();

/*
//register_shutdown_function();
set_exception_handler();
error_reporting(E_ALL);
*/

//set_error_handler([(new \sys\handlers\Error()), 'error']);

require app__ . '/confs/config.php';
require app__ . '/confs/route.php';

require sys__ . '/route.php';
require sys__ . '/init.php';

new \sys\Init();

exit();