<?php

namespace sys\components;

use sys\Init;

class Request {

	const param__ = 'ajax';

	public $method = \app\confs\request\method__;
	public $layout = \app\confs\request\layout__;

	function __construct()
	{
		Init::view()->assets->set(array('script' => 'inline'),
			'$.ajaxSetup({headers: {"' . Init::session()->key() . '": "' . Init::session()->xid() . '"}});'
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
		return $this->request('server', $key, $value);
	}

	public function post($key = null, $value = null)
	{
		return $this->request('post', $key, $value);
	}

	public function get($key = null, $value = null)
	{
		return $this->request('get', $key, $value);
	}

	public function field($subj, $key = null, $method = 'post', $separator = "_")
	{
		if (is_null($key)) {
			$request = $this->request($method);
			$names = array_keys($request);
			$length = strlen($subj . $separator);
			$context = array();
			foreach ($names as $name) {
				if (substr($name, 0, $length) === $subj . $separator) {
					$key = substr($name, $length);
					$context[$key] = $request[$name];
				}
			}
			return $context;
		}
		return $this->request($method, $subj . $separator . $key);
	}

	public function request($type = 'post', $key = null, $value = null)
	{
		switch ($type) {
			case 'server':
				if (!is_null($key) and !is_null($value))
					$_SERVER[$key] = $value;
				$request = (!is_null($key)) ? ((isset($_SERVER[$key])) ? $_SERVER[$key] : null) : $_SERVER;
				break;
			case 'post':
				if (!is_null($key) and !is_null($value))
					$_POST[$key] = $value;
				$request = (!is_null($key)) ? ((isset($_POST[$key])) ? $_POST[$key] : null) : $_POST;
				break;
			case 'get':
				if (!is_null($key) and !is_null($value))
					$_GET[$key] = $value;
				$request = (!is_null($key)) ? ((isset($_GET[$key])) ? $_GET[$key] : null) : $_GET;
				break;
			case 'request':
				$request = (!is_null($key)) ? ((isset($_REQUEST[$key])) ? $_REQUEST[$key] : null) : $_REQUEST;
				break;
			default:
				$request = null;
		}
		return $request;
	}

}