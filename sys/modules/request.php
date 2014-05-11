<?php

namespace sys\modules;

use app\confs\Request as __Request;

class Request extends \sys\Module
{

	const param__ = 'ajax';

	public $method = __Request::method__;

	public $layout = __Request::layout__;

	function __construct()
	{
	}

	public function header($key)
	{
		return $this->server('HTTP_' . strtoupper($key));
	}

	public function server($key = null, $value = null)
	{
		return $this->globals('server', $key, $value);
	}

	public function post($key = null, $value = null)
	{
		return $this->globals('post', $key, $value);
	}

	public function get($key = null, $value = null)
	{
		return $this->globals('get', $key, $value);
	}

	public function fields($subj, $method = 'post', $key = null, $separator = '_')
	{
		if (is_null($key)) {
			$request = $this->globals($method);
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
		return $this->globals($method, $subj . $separator . $key);
	}

	public function globals($global = 'post', $key = null, $value = null)
	{
		$global = '_' . strtoupper($global);
		if (!isset($GLOBALS[$global])) {
			return false;
		}
		if (!is_null($key) && !is_null($value)) {
			$GLOBALS[$global][$key] = $value;
		}
		return (!is_null($key)) ? ((isset($GLOBALS[$global][$key])) ? $GLOBALS[$global][$key] : false) : $GLOBALS[$global];
	}

}
