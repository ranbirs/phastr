<?php

namespace sys;

class Session {

	use \sys\traits\Util;

	protected $session_id;

	function __construct()
	{
		session_start();
		$this->session_id = session_id();

		if (!isset($_SESSION[$this->session_id])) {
			$this->set('_id', $this->util()->hash->id());
			$this->set('_token', $this->util()->hash->rand());
			$this->set('_key', $this->keygen());
			$this->set(['_timestamp' => 0], microtime(true));
			$this->set(['_client' => 'lang'], \app\confs\config\lang__);
		}
		$this->set(['_timestamp' => 1], microtime(true));
		if (isset($_SERVER['HTTP_USER_AGENT']))
			$this->set(['_client' => 'agent'], $_SERVER['HTTP_USER_AGENT']);
	}

	public function id()
	{
		return $this->get('_id');
	}

	public function token()
	{
		return $this->get('_token');
	}

	public function key()
	{
		return $this->get('_key');
	}

	public function keygen($hash = null)
	{
		$key = $this->util()->hash->get($this->session_id . $this->id() . $this->token(), 'sha1');
		return (!is_null($hash)) ? ($hash === $key) : $key;
	}

	public function timestamp($key = 0)
	{
		return $this->get('_timestamp', $key);
	}

	public function client($key = 'agent')
	{
		return $this->get('_client', $key);
	}

	public function user($key = 'token')
	{
		return $this->get('_user', $key);
	}

	public function get($subj, $key = null)
	{
		return (!is_null($key)) ?
			((isset($_SESSION[$this->session_id][$subj][$key])) ? $_SESSION[$this->session_id][$subj][$key] : null) :
			((isset($_SESSION[$this->session_id][$subj])) ? $_SESSION[$this->session_id][$subj] : null);
	}

	public function set($subj, $value = null)
	{
		$key = null;
		if (is_array($subj)) {
			$key = current($subj);
			$subj = key($subj);
		}
		return (!is_null($key)) ?
			$_SESSION[$this->session_id][$subj][$key] = $value :
			$_SESSION[$this->session_id][$subj] = $value;
	}

	public function drop($subj, $key = null)
	{
		if ($this->get($subj, $key)) {
			if (!is_null($key)) {
				unset($_SESSION[$this->session_id][$subj][$key]);
			}
			else {
				unset($_SESSION[$this->session_id][$subj]);
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
