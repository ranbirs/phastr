<?php
require sys__ . '/autoload.php';

require app__ . '/confs/config.php';
require app__ . '/confs/route.php';

require sys__ . '/route.class.php';
require sys__ . '/init.class.php';

new \sys\Init();

exit();