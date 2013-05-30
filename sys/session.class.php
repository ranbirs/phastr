<?php

namespace sys;

use sys\utils\Hash;

class Session {

	private $_sid, $_xid, $_gid, $_key;

	function __construct()
	{
		session_start();
		$this->_sid = session_id();

		if (!isset($_SESSION['_sid']) or $this->_sid !== $_SESSION['_sid']) {
			$this->_init();
		}

		$this->_xid = $_SESSION[$this->_sid]['_xid'];
		$this->_gid = $_SESSION['_gid'];
		$this->_key = $_SESSION['_key'];

		$_SESSION[$this->_sid]['_timestamp'][1] = microtime(true);
	}

	private function _init()
	{
		$this->_xid = Hash::rand();
		$this->_gid = Hash::rid(true);
		$this->_key = $this->keygen();

		$_SESSION['_sid'] = $this->_sid;
		$_SESSION['_gid'] = $this->_gid;
		$_SESSION['_key'] = $this->_key;

		$_SESSION[$this->_sid]['_xid'] = $this->_xid;
		$_SESSION[$this->_sid]['_timestamp'][0] = microtime(true);

		$_SESSION[$this->_sid]['_client']['ua'] = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : "";
		$_SESSION[$this->_sid]['_client']['ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION[$this->_sid]['_client']['lang'] = \app\confs\app\lang__;
	}

	public function sid()
	{
		return $this->_sid;
	}

	public function xid()
	{
		return $this->_xid;
	}

	public function key()
	{
		return $this->_key;
	}

	public function keygen($key = null)
	{
		$gen = Hash::get($this->_sid . $this->_xid . $this->_gid, 'sha1');
		return ($key) ? ($key === $gen) : $gen;
	}

	public function timestamp($key = 0, $set = false)
	{
		return ($set) ? $this->set('_timestamp', $key, microtime(true)) : $this->get('_timestamp', $key);
	}

	public function uid()
	{
		return $this->get('_user', 'uid');
	}

	public function token()
	{
		return $this->get('_user', 'token');
	}

	public function client($key = 'ua')
	{
		return $this->get('_client', $key);
	}

	public function get($type, $key = null)
	{
		$value = ($key or is_numeric($key)) ?
			((isset($_SESSION[$this->_sid][$type][$key])) ? $_SESSION[$this->_sid][$type][$key] : null) :
			((isset($_SESSION[$this->_sid][$type])) ? $_SESSION[$this->_sid][$type] : null);
		return $value;
	}

	public function set($type, $key, $value = null)
	{
		$value = (!$value and !is_numeric($value)) ? Hash::rand() : $value;
		$_SESSION[$this->_sid][$type][$key] = $value;
		return $value;
	}

	public function drop($type, $key)
	{
		if ($this->get($type, $key)) {
			unset($_SESSION[$this->_sid][$type][$key]);
			return true;
		}
		return false;
	}

	public function quit()
	{
		session_unset();
		session_destroy();
	}

}
