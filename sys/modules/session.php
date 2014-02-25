<?php

namespace sys\modules;

use app\confs\Config as ConfigConf;

class Session extends \sys\components\Session
{
	
	use \sys\traits\module\Hash;

	function __construct()
	{
		$this->start((\app\confs\Database::session__) ? new \sys\handlers\session\Database() : null);
	}

	public function generate()
	{
		$this->set('_token', $this->hash()->rand('md5'));
		$this->set('_key', $this->keygen());
		$this->set(['_timestamp' => 0], microtime(true));
		$this->set(['_client' => 'lang'], ConfigConf::lang__);
	}

	public function register()
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
		$key = $this->hash()->gen($this->session_id . $this->token(), 'sha256');
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

}
