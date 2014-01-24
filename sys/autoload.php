<?php

namespace sys;

function autoload($class) {

	$class = strtolower(ltrim($class, '\\'));
	$namespace = explode('\\', $class, 2);
	$path = (isset($namespace[1])) ? explode('\\', $namespace[1]) : [];
	$subj = rtrim(current($path), 's');

	$conf = app__ . '/confs/' . end($path) . '.php';
	if (stream_resolve_include_path($conf) !== false) {
		require_once $conf;
	}
	switch ($base = current($namespace)) {
		case app__:
			$path = implode('/', $path);
			require_once $base . '/' . $path . '.' . $subj . '.php';
			break;
		case sys__:
			$path = implode('/', explode('\\', $class));
			$file = $path . '.class.php';
			if (stream_resolve_include_path($file) === false)
				$file = $path . '.' . $subj . '.php';
			require_once $file;
			break;
	}
}