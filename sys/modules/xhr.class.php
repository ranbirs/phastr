<?php

namespace sys\modules;

class Xhr {

	protected $view;

	private $_xid, $_key;

	function __construct()
	{
		$this->view = \sys\Res::view();

		$this->_xid = \sys\Session::xid();
		$this->_key = \sys\Session::key();

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
		$subj = \sys\Res::get('params', 0);
		$type = \sys\Res::get('params', 1);

		if (!$this->token() or $subj !== \app\confs\sys\xhr_param__) {
			return false;
		}
		switch ($type) {
			case 'post':
			case 'get':
				return $this->$type();
			break;
			case 'form':
				$method = \sys\Res::get('params', 2);
				$fid = \sys\Res::get('params', 3);
				$key = $this->_key;

				if ($method and method_exists($this, $method)) {
					if ($this->$method("form_fid_$key") === $fid) {
						return $this->$method();
					}
				}
			break;
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
				$request = ($key) ? ((isset($_SERVER[$key])) ? $_SERVER[$key] : null) : $_SERVER;
			break;
			case 'get':
				if ($value !== null)
					$_GET[$key] = $value;
				$request = ($key) ? ((isset($_GET[$key])) ? $_GET[$key] : null) : $_GET;
			break;
			case 'post':
				if ($value !== null)
					$_POST[$key] = $value;
				$request = ($key) ? ((isset($_POST[$key])) ? $_POST[$key] : null) : $_POST;
			break;
			default:
				return false;
			break;
		}
		return $request;
	}

}
