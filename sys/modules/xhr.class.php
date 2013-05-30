<?php

namespace sys\modules;

use sys\Res;

class Xhr {

	protected $view;
	private $_xid, $_key;

	function __construct()
	{
		$this->view = Res::view();
		$this->_xid = Res::session()->xid();
		$this->_key = Res::session()->key();

		$this->view->assets('script', null,
			'$.ajaxSetup({headers: {"' . $this->_key . '": "' . $this->_xid . '"}});'
		);
	}

	public function server($key = null, $value = null)
	{
		return $this->_request('server', $key, $value);
	}

	public function get($key = null, $value = null)
	{
		return $this->_request('get', $key, $value);
	}

	public function post($key = null, $value = null)
	{
		return $this->_request('post', $key, $value);
	}

	public function token($key = "")
	{
		$key = "HTTP_" . strtoupper($key . $this->_key);
		$token = $this->server($key);
		return ($token === $this->_xid) ? $token : null;
	}

	public function request()
	{
		$subj = Res::get('params', 0);
		$type = Res::get('params', 1);

		if (!$this->token() or $subj !== \app\confs\sys\xhr_param__) {
			return false;
		}
		switch ($type) {
			case 'post':
			case 'get':
				$request = $this->$type();
				break;
			case 'form':
				$method = Res::get('params', 2);
				$fid = Res::get('params', 3);
				$key = $this->_key;

				if ($method and method_exists($this, $method)) {
					if ($this->$method("form_fid_$key") === $fid) {
						$request = $this->$method();
						break;
					}
				}
				$request = null;
				break;
			default:
				$request = null;
		}
		return $request;
	}

	public function response($data, $format = 'json')
	{
		$this->view->response = $data;
		$this->view->layout(\app\confs\sys\xhr_param__ . "/$format");
	}

	private function _request($type = 'post', $key = null, $value = null)
	{
		switch ($type) {
			case 'server':
				if (!is_null($value))
					$_SERVER[$key] = $value;
				$request = ($key) ? ((isset($_SERVER[$key])) ? $_SERVER[$key] : null) : $_SERVER;
				break;
			case 'get':
				if (!is_null($value))
					$_GET[$key] = $value;
				$request = ($key) ? ((isset($_GET[$key])) ? $_GET[$key] : null) : $_GET;
				break;
			case 'post':
				if (!is_null($value))
					$_POST[$key] = $value;
				$request = ($key) ? ((isset($_POST[$key])) ? $_POST[$key] : null) : $_POST;
				break;
			case 'request':
				$request = ($key) ? ((isset($_REQUEST[$key])) ? $_REQUEST[$key] : null) : $_REQUEST;
				break;
			default:
				$request = null;
		}
		return $request;
	}

}
