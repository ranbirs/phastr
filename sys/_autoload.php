<?php

namespace sys;

	function path_base($path = "", $base = app__) {
		return ($path) ? constant($base . "_base__") . "/" . $path : $path;
	}

	function _autoload($class) {

		$class = strtolower(ltrim($class, "\\"));
		$namespace = explode("\\", $class, 2);
		$path = explode("\\", $namespace[1]);

		$conf_file = \sys\path_base('confs/' . end($path)) . ".php";
		if (stream_resolve_include_path($conf_file) !== false) {
			require_once $conf_file;
		}
		switch ($namespace[0]) {
			case app__:
				$path = implode("/", $path);
				require_once \sys\path_base($path, $namespace[0]) . ".php";
				break;
			case sys__:
				$path = implode("/", explode("\\", $class));
				$file = $path . ".class.php";
				if (stream_resolve_include_path($file) === false)
					$file = $path . "." . rtrim(current(explode("\\", $namespace[1], 2)), "s") . ".php";
				require_once $file;
				break;
		}
	}

call_user_func(function () {
	$app_dir = app__ . "/" . $_SERVER[app_server__];
	define("app_base__", (is_dir(get_include_path() . "/" . $app_dir)) ? $app_dir : app__ . "/default");
	define("sys_base__", sys__);
});

spl_autoload_register("\\sys\\_autoload");
