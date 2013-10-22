<?php

namespace sys\modules;

use sys\Init;

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

	public function fields($subj, $method = 'post', $key = null, $separator = "_")
	{
		if (is_null($key)) {
			$request = $this->request($method);
			$names = array_keys($request);
			$length = strlen($subj . $separator);
			$context = [];
			foreach ($names as $name) {
				if (substr($name, 0, $length) === $subj . $separator) {
					$key = substr($name, $length);
					if (substr($key, 0, 1) !== $separator) {
						$context[$key] = $request[$name];
					}
				}
			}
			return $context;
		}
		return $this->request($method, $subj . $separator . $key);
	}

	public function request($global = 'post', $key = null, $value = null)
	{
		switch ($global) {
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
				return false;
		}
		return $request;
	}

}
