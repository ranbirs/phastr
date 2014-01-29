<?php

namespace sys\modules;

use sys\Module;

class Request extends Module
{
	use \sys\traits\Instance;
	const method__ = \app\confs\Request::method__;
	const layout__ = \app\confs\Request::layout__;
	const param__ = 'ajax';

	public $method = self::method__;

	public $layout = self::layout__;

	function __construct()
	{
		$this->view()->assets()->set(['script' => 'inline'], 
			'$.ajaxSetup({headers: {\'' . $this->session()->key() . '\': \'' . $this->session()->token() . '\'}});');
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
		if (! isset($GLOBALS[$global])) {
			return false;
		}
		if (! is_null($key) && ! is_null($value)) {
			$GLOBALS[$global][$key] = $value;
		}
		return (! is_null($key)) ? ((isset($GLOBALS[$global][$key])) ? $GLOBALS[$global][$key] : false) : $GLOBALS[$global];
	}

	public function resolve($params = [])
	{
		if (! isset($params[2]) || $params[0] !== self::param__) {
			return false;
		}
		if ($this->header($this->session()->key()) !== $this->session()->token()) {
			return false;
		}
		$subj = $params[2];
		switch ($context = $params[1]) {
			case 'request':
				$this->view()->request = $this->globals($this->method);
				$this->view()->response = $this->view()->request($subj);
				if ($this->view()->response !== false) {
					return true;
				}
				return false;
			case 'form':
				if ($this->instance()->$subj instanceof \sys\modules\Form) {
					$this->view()->request = $this->globals($this->instance()->$subj->method());
					$this->view()->response = $this->instance()->$subj->resolve('json');
					return true;
				}
				return false;
			default:
				return false;
		}
	}

}
