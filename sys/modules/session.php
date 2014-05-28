<?php

namespace sys\modules;

use app\confs\Config as __config;
use app\confs\Database as __database;

class Session extends \sys\components\Session
{
	
	use \sys\Loader;

	function __construct()
	{
		$this->load()->module('hash');
		$this->start((__database::session__) ? new \sys\handlers\session\Database() : null);
	}

	public function generate()
	{
		$this->set('_token', $this->hash->rand('md5'));
		$this->set('_key', $this->keygen());
		$this->set(['_timestamp' => 0], microtime(true));
		$this->set(['_client' => 'lang'], __config::lang__);
	}

	public function register()
	{
		$this->set(['_timestamp' => 1], microtime(true));
		$this->set(['_client' => 'agent'], (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : null);
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
		$key = $this->hash->gen($this->session_id . $this->token(), 'sha256');
		return (!$hash) ? $key : ($hash === $key);
	}

	public function timestamp($key = 0)
	{
		return $this->get(['_timestamp' => $key]);
	}

	public function client($key = 'agent')
	{
		return $this->get(['_client' => $key]);
	}

}
