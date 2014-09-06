<?php

spl_autoload_register();

$error_handler = new \sys\handlers\Error();

set_error_handler([$error_handler, 'error']);
set_exception_handler([$error_handler, 'exception']);

require app__ . '/configs/route.php';

require sys__ . '/route.php';
require sys__ . '/init.php';

new \sys\Init();

exit();