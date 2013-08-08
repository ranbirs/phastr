<?php

namespace sys;

	function base_def() {
		$server = app__ . "/" . $_SERVER[server__];
		define("base_app__", (is_dir(get_include_path() . "/" . $server)) ? $server : app__ . "/default");
		define("base_sys__", sys__);
	}

	function base($path = null, $base = app__) {
		$base = constant("base_" . $base . "__");
		return ($path) ? $base . "/" . $path : $path;
	}

	function app_autoload($class) {
		$class = explode("\\", ltrim($class, "\\"), 2);
		$path = implode("/", explode("\\", $class[1]));
		require_once strtolower(\sys\base($path, $class[0])) . ".php";
	}

\sys\base_def();

spl_autoload_extensions(".class.php,.php");
spl_autoload_register();

spl_autoload_register("\\sys\\app_autoload");
