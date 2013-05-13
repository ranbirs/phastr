<?php

require \sys\sys_base__ . "utils/helper.class.php";
require \sys\sys_base__ . "load.class.php";

\sys\Load::conf('constants');
\sys\Load::vocab('sys', false);

\sys\Load::sys('init');
\sys\Init::start();
