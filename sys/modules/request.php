<?php

namespace sys\modules;

use app\confs\Request as __request;

class Request
{

	public $method = __request::method__;

	public function header($key = null)
	{
		return $this->server(($key) ? 'HTTP_' . strtoupper($key) : null);
	}

	public function server($key = null)
	{
		return ($key) ? ((isset($_SERVER[$key])) ? $_SERVER[$key] : false) : $_SERVER;
	}

	public function post($key = null, $value = null)
	{
		if ($key && !is_null($value)) {
			return $_POST[$key] = $value;
		}
		return ($key) ? ((isset($_POST[$key])) ? $_POST[$key] : false) : $_POST;
	}

	public function get($key = null, $value = null)
	{
		if ($key && !is_null($value)) {
			return $_GET[$key] = $value;
		}
		return ($key) ? ((isset($_GET[$key])) ? $_GET[$key] : false) : $_GET;
	}

	public function fields($subj, $method = 'post', $key = null, $delimiter = '_')
	{
		if ($key) {
			return $this->{$method}($subj . $delimiter . $key);
		}
		$request = $this->{$method}();
		$labels = array_keys($request);
		$length = strlen($subj . $delimiter);
		$fields = [];

		foreach ($labels as $label) {
			if (substr($label, 0, $length) == $subj . $delimiter && substr($key = substr($label, $length), 0, 1) != $delimiter) {
				$fields[$key] = $request[$label];
			}
		}
		return $fields;	
	}

}
