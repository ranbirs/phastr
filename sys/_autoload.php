<?php

namespace sys;

	function app_autoload($class) {
		$class = explode("\\", ltrim($class, "\\"), 2);
		$path = implode("/", explode("\\", $class[1]));
		require_once strtolower(\sys\base($path, $class[0])) . ".php";
	}

spl_autoload_extensions(".class.php,.php");
spl_autoload_register();
spl_autoload_register("\\sys\\app_autoload");
