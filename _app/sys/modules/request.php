<?php

namespace sys\modules;

use app\confs\Request as __request;

class Request
{
	
	use \sys\Loader;

	public $method = __request::method__;

	public $format = __request::format__;
	
	public function method($method = null)
	{
		return ($method) ? $this->method = $method : $this->method;
	}
	
	public function format($format = null)
	{
		return ($format) ? $this->format = $format : $this->format;
	}

	public function header($key = '')
	{
		return $this->server(($key) ? 'HTTP_' . strtoupper($key) : null);
	}

	public function server($key = '')
	{
		return ($key) ? ((isset($_SERVER[$key])) ? $_SERVER[$key] : false) : $_SERVER;
	}

	public function post($key = '', $value = null)
	{
		if ($key && !is_null($value)) {
			return $_POST[$key] = $value;
		}
		return ($key) ? ((isset($_POST[$key])) ? $_POST[$key] : false) : $_POST;
	}

	public function get($key = '', $value = null)
	{
		if ($key && !is_null($value)) {
			return $_GET[$key] = $value;
		}
		return ($key) ? ((isset($_GET[$key])) ? $_GET[$key] : false) : $_GET;
	}

	public function fields($subj, $method = 'post', $key = '', $delimiter = '_')
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

	public function resolve($instance, $subj)
	{
		if (!$this->load()->module('validation')->validate('request')) {
			return false;
		}
		if (method_exists($instance->{$subj}, 'method')) {
			$this->method($instance->{$subj}->method());
		}
		if (method_exists($instance->{$subj}, 'format')) {
			$this->format($instance->{$subj}->format());
		}
		if (method_exists($instance->{$subj}, 'request')) {
			return $instance->{$subj}->request();
		}
		return false;
	}

}
