<?php
spl_autoload_register();

require app__ . '/confs/config.php';
require app__ . '/confs/route.php';

require sys__ . '/route.php';
require sys__ . '/init.php';

new \sys\Init();

exit();