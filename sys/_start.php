<?php

require sys__ . "/_autoload.php";
require sys__ . "/confs/error.php";

require \sys\path_base("confs/config.php");
require \sys\path_base("confs/route.php");

require sys__ . "/route.class.php";
require sys__ . "/init.class.php";

new \sys\Init();

exit;
