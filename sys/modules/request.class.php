<?php

namespace sys\modules;

use sys\Module;

class Request extends Module {

	const method__ = \app\confs\request\method__;
	const layout__ = \app\confs\request\layout__;

	const param__ = 'ajax';

	public $method = self::method__;
	public $layout = self::layout__;

	function __construct()
	{
		$this->view()->assets->set(['script' => 'inline'],
			'$.ajaxSetup({headers: {\'' . $this->session()->key() . '\': \'' . $this->session()->token() . '\'}});'
		);
	}

	public function header($key = null)
	{
		if (is_null($key))
			$key = $this->session()->key();
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
				if (substr($key = substr($label, $length), 0, 1) !== $separator)
					$fields[$key] = $request[$label];
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
		if (!is_null($key) && !is_null($value))
			$GLOBALS[$global][$key] = $value;
		return (!is_null($key)) ? ((isset($GLOBALS[$global][$key])) ? $GLOBALS[$global][$key] : false) : $GLOBALS[$global];
	}

	public function resolve()
	{
		$context = $this->route()->params(1);
		$subj = $this->route()->params(2);
	
		if (is_null($subj) || $this->header() !== $this->session()->token()) {
			return false;
		}
		switch ($context) {
			case 'request':
				$this->view()->request = $this->globals($this->method);
				$this->view()->response = $this->view()->request($subj);
				if ($this->view()->response !== false) {
					return true;
				}
				return false;
			case 'form':
				if ($this->load()->$subj instanceof \sys\modules\Form) {
					$this->view()->request = $this->globals($this->load()->$subj->method());
					$this->view()->response = $this->load()->$subj->resolve('json');
					return true;
				}
				return false;
			default:
				return false;
		}
	}

}
