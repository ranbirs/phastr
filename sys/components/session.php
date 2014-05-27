<?php

namespace sys\components;

abstract class Session
{

	protected $session_id;

	abstract public function generate();

	abstract public function register();

	public function session_id()
	{
		return $this->session_id;
	}

	public function start($handler = null)
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			if ($handler) {
				session_set_save_handler($handler);
				register_shutdown_function('session_write_close');
			}
			session_start();
		}
		$this->session_id = session_id();
		if (!isset($_SESSION[$this->session_id])) {
			$_SESSION = [$this->session_id => []];
			$this->generate();
		}
		$this->register();
	}

	public function destroy()
	{
		session_unset();
		session_destroy();
	}

	public function reset()
	{
		$this->destroy();
		$this->start();
	}

	public function get($subj, $key = null)
	{
		if (!is_null($key)) {
			return (isset($_SESSION[$this->session_id][$subj][$key])) ? $_SESSION[$this->session_id][$subj][$key] : false;
		}
		return (isset($_SESSION[$this->session_id][$subj])) ? $_SESSION[$this->session_id][$subj] : false;
	}

	public function set($subj, $value = null)
	{
		$key = null;
		if (is_array($subj)) {
			$key = current($subj);
			$subj = key($subj);
		}
		return (!is_null($key)) ? $_SESSION[$this->session_id][$subj][$key] = $value : $_SESSION[$this->session_id][$subj] = $value;
	}

	public function drop($subj, $key = null)
	{
		if (!is_null($key)) {
			unset($_SESSION[$this->session_id][$subj][$key]);
		}
		else {
			unset($_SESSION[$this->session_id][$subj]);
		}
	}

}
