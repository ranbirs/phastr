<?php

namespace sys;

	function base_def() {
		$hostdir = app__ . "/" . $_SERVER[server__];
		$default = app__ . "/default";
		define("base_app__", (is_dir(get_include_path() . "/" . $hostdir)) ? $hostdir : $default);
		define("base_sys__", sys__);
	}
	function base($path = null, $base = app__) {
		$base = constant("base_" . $base . "__");
		return ($path) ? $base . "/" . $path : $path;
	}

\sys\base_def();

require sys__ . "/_autoload.php";

require \sys\base("confs/config.php");
require \sys\base("confs/rewrite.php");
require \sys\base("confs/error.php", sys__);

require \sys\base("route.class.php", sys__);
require \sys\base("init.class.php", sys__);

new \sys\Init();
