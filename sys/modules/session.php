<?php

namespace sys\modules;

class Session
{
	use \sys\traits\Util;

	protected $session_id;

	function __construct()
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		$this->start();
	}

	public function start()
	{
		$this->session_id = session_id();
		if (! isset($_SESSION[$this->session_id])) {
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

	public function id()
	{
		return $this->session_id;
	}

	protected function generate()
	{
		$this->set('_token', $this->util()->hash()->rand());
		$this->set('_key', $this->keygen());
		$this->set(['_timestamp' => 0], microtime(true));
		$this->set(['_client' => 'lang'], \app\confs\Config::lang__);
	}

	protected function register()
	{
		$this->set(['_timestamp' => 1], microtime(true));
		$this->set(['_client' => 'agent'], 
			(isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : null);
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
		$key = $this->util()->hash()->gen($this->session_id . $this->token(), 'sha1');
		return (is_null($hash)) ? $key : ($hash === $key);
	}

	public function timestamp($key = 0)
	{
		return $this->get('_timestamp', $key);
	}

	public function client($key = 'agent')
	{
		return $this->get('_client', $key);
	}

	public function get($subj, $key = null)
	{
		return (! is_null($key)) ? ((isset($_SESSION[$this->session_id][$subj][$key])) ? $_SESSION[$this->session_id][$subj][$key] : null) : ((isset(
			$_SESSION[$this->session_id][$subj])) ? $_SESSION[$this->session_id][$subj] : null);
	}

	public function set($subj, $value = null)
	{
		$key = null;
		if (is_array($subj)) {
			$key = current($subj);
			$subj = key($subj);
		}
		return (! is_null($key)) ? $_SESSION[$this->session_id][$subj][$key] = $value : $_SESSION[$this->session_id][$subj] = $value;
	}

	public function drop($subj, $key = null)
	{
		if ($this->get($subj, $key)) {
			if (! is_null($key)) {
				unset($_SESSION[$this->session_id][$subj][$key]);
			}
			else {
				unset($_SESSION[$this->session_id][$subj]);
			}
			return true;
		}
		return false;
	}

}
