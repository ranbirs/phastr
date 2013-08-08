<?php

require sys__ . "/_autoload.php";

require \sys\base("confs/error.php", sys__);
require \sys\base("confs/config.php");
require \sys\base("confs/rewrite.php");

require \sys\base("route.class.php", sys__);
require \sys\base("init.class.php", sys__);

new \sys\Init();
