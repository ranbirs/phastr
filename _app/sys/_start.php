<?php
spl_autoload_register();

$error_handler = new \sys\handlers\Error();

set_error_handler([$error_handler, 'error']);
set_exception_handler([$error_handler, 'exception']);

require app__ . '/confs/config.php';
require app__ . '/confs/route.php';

require sys__ . '/route.php';
require sys__ . '/init.php';

require sys__ . '/utils/helper.php';
require sys__ . '/utils/path.php';
require sys__ . '/utils/html.php';

new \sys\Init();

exit();