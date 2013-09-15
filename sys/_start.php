<?php

require sys__ . "/_autoload.php";
require sys__ . "/confs/error.php";

require \sys\base("confs/config.php");
require \sys\base("confs/rewrite.php");

require sys__ . "/route.class.php";
require sys__ . "/init.class.php";

new \sys\Init();

exit();
