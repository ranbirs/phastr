<?php

require \sys\sys_base__ . "utils/helper.class.php";
require \sys\sys_base__ . "load.class.php";

\sys\Load::conf('constants');
\sys\Load::vocab('sys', false);

require \sys\sys_base__ . "init.class.php";
require \sys\sys_base__ . "res.class.php";

\sys\Init::start();
