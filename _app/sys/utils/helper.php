<?php

namespace sys\utils\helper;

function class_name($instance) {
	$class = explode('\\', get_class($instance));
	return end($class);
}

function args($arg = null, $delimiter = ':') {
	$args = [];
	foreach ((array) $arg as $params) {
		$params = array_map('trim', explode($delimiter, $params, 2));
		if (isset($params[0]) && strlen($params[0])) {
			$args[$params[0]] = (isset($params[1])) ? $params[1] : null;
		}
	}
	return $args;
}

function attr($attr = [], $glue = ' ') {
	$attrs = [];
	foreach ((array) $attr as $key => $val) {
		if (is_array($val)) {
			$val = implode($glue, $val);
		}
		if (is_int($key)) {
			$key = $val;
			$val = '';
		}
		$attrs[$key] = $val;
	}
	return $attrs;
}

function trim_array($subj = null) {
	return array_map('trim', $subj);
}

function filter_split($delimiter, $subj = null, $limit = -1, $flags = PREG_SPLIT_NO_EMPTY) {
    return preg_split('/' . preg_quote($delimiter, '/') . '/', $subj, $limit, $flags);
}

function iterate_join($subj = [], $glue = null, $prepend = null, $append = null) {
	$join = [];
	foreach ($subj as $key => $val) {
		$join[] = (strlen($val)) ? $prepend . $key . $glue . $val . $append : $key;
	}
	return $join;
}
