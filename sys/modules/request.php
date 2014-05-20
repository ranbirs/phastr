<?php

namespace sys\modules;

use app\confs\Request as __request;

class Request extends \sys\Module
{

	const param__ = 'ajax';

	public $method = __request::method__;

	public $layout = __request::layout__;

	function __construct()
	{
	}

	public function header($key = null)
	{
		return $this->server((!is_null($key)) ? 'HTTP_' . strtoupper($key) : null);
	}

	public function server($key = null)
	{
		return (!is_null($key)) ? ((isset($_SERVER[$key])) ? $_SERVER[$key] : false) : $_SERVER;
	}
	
	public function post($key = null, $value = null)
	{
		if (!is_null($key) && !is_null($value)) {
			return $_POST[$key] = $value;
		}
		return (!is_null($key)) ? ((isset($_POST[$key])) ? $_POST[$key] : false) : $_POST;
	}

	public function get($key = null, $value = null)
	{
		if (!is_null($key) && !is_null($value)) {
			return $_GET[$key] = $value;
		}
		return (!is_null($key)) ? ((isset($_GET[$key])) ? $_GET[$key] : false) : $_GET;
	}

	public function fields($subj, $method = 'post', $key = null, $separator = '_')
	{
		if (is_null($key)) {
			$request = $this->{$method}();
			$labels = array_keys($request);
			$length = strlen($subj . $separator);
			$fields = [];
			foreach ($labels as $label) {
				if (substr($label, 0, $length) !== $subj . $separator) {
					continue;
				}
				if (substr($key = substr($label, $length), 0, 1) !== $separator) {
					$fields[$key] = $request[$label];
				}
			}
			return $fields;
		}
		return $this->{$method}($subj . $separator . $key);
	}
	


}
