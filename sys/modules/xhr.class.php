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
		if ($token === $this->_xid) {
			return $token;
		}
		return false;
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
				return $this->$type();
			case 'form':
				$method = Res::get('params', 2);
				$fid = Res::get('params', 3);
				$key = $this->_key;

				if ($method and method_exists($this, $method)) {
					if ($this->$method("form_fid_$key") === $fid) {
						return $this->$method();
					}
				}
		}
		return false;
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
				if ($value !== null)
					$_SERVER[$key] = $value;
				return ($key) ? ((isset($_SERVER[$key])) ? $_SERVER[$key] : null) : $_SERVER;
			case 'get':
				if ($value !== null)
					$_GET[$key] = $value;
				return ($key) ? ((isset($_GET[$key])) ? $_GET[$key] : null) : $_GET;
			case 'post':
				if ($value !== null)
					$_POST[$key] = $value;
				return ($key) ? ((isset($_POST[$key])) ? $_POST[$key] : null) : $_POST;
		}
	}

}
