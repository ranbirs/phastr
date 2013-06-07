<?php

namespace sys;

use sys\utils\Hash;

class Session {

	private $_sid;

	function __construct()
	{
		$this->start();
	}

	private function _init()
	{
		$this->set('_xid');
		$this->set('_gid', Hash::rid(true));
		$this->set('_key', $this->keygen());

		$this->timestamp(0, true);

		if (isset($_SERVER['HTTP_USER_AGENT']))
			$this->set(array('_client' => 'ua'), $_SERVER['HTTP_USER_AGENT']);
		$this->set(array('_client' => 'ip'), $_SERVER['REMOTE_ADDR']);
		$this->set(array('_client' => 'lang'), \app\confs\app\lang__);
	}

	public function start()
	{
		session_start();
		$this->_sid = session_id();

		if (!isset($_SESSION['_sid']) or $this->_sid !== $_SESSION['_sid']) {
			$_SESSION['_sid'] = $this->_sid;
			$this->_init();
		}

		$this->timestamp(1, true);
	}

	public function sid()
	{
		return $this->_sid;
	}

	public function xid()
	{
		return $this->get('_xid');
	}

	public function key()
	{
		return $this->get('_key');
	}

	public function keygen($hash = null)
	{
		$keygen = Hash::get($this->_sid . $this->xid() . $this->gid(), 'sha1');
		return ($hash) ? ($hash === $keygen) : $keygen;
	}

	public function timestamp($key = 0, $set = false)
	{
		return ($set) ? $this->set(array('_timestamp' => $key), microtime(true)) : $this->get('_timestamp', $key);
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

	public function get($subj, $key = null)
	{
		$value = ($key or is_numeric($key)) ?
			((isset($_SESSION[$this->_sid][$subj][$key])) ? $_SESSION[$this->_sid][$subj][$key] : null) :
			((isset($_SESSION[$this->_sid][$subj])) ? $_SESSION[$this->_sid][$subj] : null);
		return $value;
	}

	public function set($subj, $value = null)
	{
		$key = null;
		if (is_array($subj)) {
			$key = current(array_values($subj));
			$subj = current(array_keys($subj));
		}
		$value = (!$value and !is_numeric($value)) ? Hash::rand() : $value;
		($key or is_numeric($key)) ?
			$_SESSION[$this->_sid][$subj][$key] = $value :
			$_SESSION[$this->_sid][$subj] = $value;
		return $value;
	}

	public function drop($subj, $key = null)
	{
		if ($this->get($subj, $key)) {
			if ($key or is_numeric($key)) {
				unset($_SESSION[$this->_sid][$subj][$key]);
				return true;
			}
			unset($_SESSION[$this->_sid][$subj]);
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
