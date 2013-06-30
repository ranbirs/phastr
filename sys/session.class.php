<?php

namespace sys;

use sys\utils\Hash;

class Session {

	private $_sid;

	function __construct()
	{
		session_start();
		$this->_sid = session_id();

		if (!isset($_SESSION['_sid']) or $this->_sid !== $_SESSION['_sid']) {
			$_SESSION['_sid'] = $this->_sid;

			$this->timestamp(0, true);
			$this->set('_xid', Hash::rand());
			$this->set('_gid', Hash::rid(true));
			$this->set('_key', $this->keygen());
			$this->set(array('_client' => 'lang'), \app\confs\app\lang__);
		}
		$this->timestamp(1, true);
		if (isset($_SERVER['HTTP_USER_AGENT']))
			$this->set(array('_client' => 'agent'), $_SERVER['HTTP_USER_AGENT']);
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

	public function uid()
	{
		return $this->get('_user', 'uid');
	}

	public function token()
	{
		return $this->get('_user', 'token');
	}

	public function client($key = 'agent')
	{
		return $this->get('_client', $key);
	}

	public function keygen($hash = null)
	{
		$keygen = Hash::get($this->_sid . $this->xid() . $this->get('_gid'), 'sha1');
		return (!is_null($hash)) ? ($hash === $keygen) : $keygen;
	}

	public function timestamp($key = 0, $set = false)
	{
		return ((bool) $set) ? $this->set(array('_timestamp' => $key), microtime(true)) : $this->get('_timestamp', $key);
	}

	public function get($subj, $key = null)
	{
		return (!is_null($key)) ?
			((isset($_SESSION[$this->_sid][$subj][$key])) ? $_SESSION[$this->_sid][$subj][$key] : null) :
			((isset($_SESSION[$this->_sid][$subj])) ? $_SESSION[$this->_sid][$subj] : null);
	}

	public function set($subj, $value = null)
	{
		$key = null;
		if (is_array($subj)) {
			$key = current($subj);
			$subj = key($subj);
		}
		return (!is_null($key)) ?
			$_SESSION[$this->_sid][$subj][$key] = $value :
			$_SESSION[$this->_sid][$subj] = $value;
	}

	public function drop($subj, $key = null)
	{
		if ($this->get($subj, $key)) {
			if (!is_null($key)) {
				unset($_SESSION[$this->_sid][$subj][$key]);
			}
			else {
				unset($_SESSION[$this->_sid][$subj]);
			}
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
