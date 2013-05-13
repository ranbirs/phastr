<?php

namespace sys;

	const app_base__ = "app/";
	const sys_base__ = "sys/";

set_include_path(dirname(__DIR__));

require \sys\sys_base__ . "_autoload.php";
require \sys\sys_base__ . "_start.php";
