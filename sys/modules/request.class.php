<?php

namespace sys\modules;

use \sys\Init;

class Request {

	const method__ = \app\confs\request\method__;
	const layout__ = \app\confs\request\layout__;

	const param__ = 'ajax';

	public $method = self::method__;
	public $layout = self::layout__;

	function __construct()
	{
		Init::view()->assets->set(['script' => 'inline'],
			'$.ajaxSetup({headers: {"' . Init::session()->key() . '": "' . Init::session()->token() . '"}});'
		);
	}

	public function header($key = null)
	{
		if (is_null($key))
			$key = Init::session()->key();
		return $this->server("HTTP_" . strtoupper($key));
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

	public function fields($subj, $method = 'post', $key = null, $separator = "_")
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
				if (substr($key = substr($label, $length), 0, 1) !== $separator)
					$fields[$key] = $request[$label];
			}
			return $fields;
		}
		return $this->globals($method, $subj . $separator . $key);
	}

	public function globals($global = 'post', $key = null, $value = null)
	{
		$global = "_" . strtoupper($global);
		if (!isset($GLOBALS[$global])) {
			return false;
		}
		if (!is_null($key) and !is_null($value))
			$GLOBALS[$global][$key] = $value;
		return (!is_null($key)) ? ((isset($GLOBALS[$global][$key])) ? $GLOBALS[$global][$key] : false) : $GLOBALS[$global];
	}

	public function resolve()
	{
		$context = Init::route()->params(1);
		$subj = Init::route()->params(2);
	
		if (is_null($subj) or $this->header() !== Init::session()->token()) {
			return false;
		}
		switch ($context) {
			case 'request':
				Init::view()->request = $this->globals($this->method);
				Init::view()->response = Init::view()->request($subj);
				if (Init::view()->response !== false) {
					return true;
				}
				return false;
			case 'form':
				if (Init::load()->$subj instanceof \sys\modules\Form) {
					Init::view()->request = $this->globals(Init::load()->$subj->method());
					Init::view()->response = Init::load()->$subj->resolve('json');
					return true;
				}
				return false;
			default:
				return false;
		}
	}

}
