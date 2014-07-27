<?php

namespace sys\utils\path;
	
function route($key = null) {
	return \sys\Init::$route->path($key);
}

function resolve($file) {
	return (($file = stream_resolve_include_path($file)) !== false) ? $file : false;
}

function label($path = null) {
	return str_replace('-', '_', $path);
	// return str_replace('-', '_', preg_replace('/[^a-z0-9]/', '', $path));
}

function file($path, $base = app__, $ext = 'php') {
	return \sys\utils\path\label($base . '/' . $path) . '.' . $ext;
}

function root($path = null) {
	return ($path) ? $_SERVER['DOCUMENT_ROOT'] . '/' . $path : $_SERVER['DOCUMENT_ROOT'];
}

function base($path = null) {
	return \sys\utils\path\route('base') . $path;
}

function page($path = null) {
	return \sys\utils\path\route('label')[0] . '/' . (($path) ? \sys\utils\path\label($path) : \sys\utils\path\route('label')[1]);
}

function uri($path = null) {
	$uri = (\app\confs\Route::rewrite__) ? \sys\utils\path\route('base') : \sys\utils\path\route('file');
	return $uri .= ($path && $path != '/') ? '/' . $path : ''; // \sys\utils\path\route('uri');
}

function request($path = null) {
	return \sys\utils\path\uri(\sys\utils\path\route('route') . '/' . \app\confs\Route::request__ . '/' . $path);
}

function trail($path = null) {
	return ($path) ? $path . \app\confs\Route::trail__ : '';
}
