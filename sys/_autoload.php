<?php

namespace sys;

	function _base_def() {
		$app_dir = app__ . "/" . $_SERVER[app_server__];
		define("base_app__", (is_dir(get_include_path() . "/" . $app_dir)) ? $app_dir : app__ . "/default");
		define("base_sys__", sys__);
	}

	function base_path($path = null, $base = app__) {
		return ($path) ? constant("base_" . $base . "__") . "/" . $path : $path;
	}

	function app_autoload($class) {
		$class = explode("\\", ltrim($class, "\\"), 2);
		$path = implode("/", explode("\\", $class[1]));
		require_once strtolower(\sys\base_path($path, $class[0])) . ".php";
	}

\sys\_base_def();

spl_autoload_extensions(".class.php,.php");
spl_autoload_register();

spl_autoload_register("\\sys\\app_autoload");
