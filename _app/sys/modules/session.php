<?php

namespace sys\modules;

use app\confs\Config as __config;
use app\confs\Database as __database;

class Session extends \sys\components\Session
{
	
	use \sys\Loader;

	function __construct()
	{
		$this->start((__database::session__) ? new \sys\handlers\session\Database() : null);
	}

	public function generate()
	{
		$this->set(['_timestamp' => 0], $this->timestamp(true));
		$this->set('_token', $this->token(true));
		$this->set('_key', $this->keygen());
		$this->set(['_client' => 'lang'], __config::lang__);
	}

	public function register()
	{
		$this->set(['_client' => 'agent'], (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : null);
	}
	
	public function render()
	{
		$this->set('_request', $this->hash($this->set(['_timestamp' => 1], $this->timestamp(true))));
	}

	public function timestamp($gen = false)
	{
		if (!$gen) {
			return $this->get('_timestamp');
		}
		return uniqid(microtime(true) . '.', true);
	}
	
	public function token($gen = false)
	{
		if (!$gen) {
			return $this->get('_token');
		}
		return $this->load()->module('hash')->gen($this->session_id, 'sha1', $this->timestamp()[0]);
	}

	public function key()
	{
		return $this->get('_key');
	}

	public function keygen($hash = '')
	{
		$key = $this->load()->module('hash')->gen($this->get('_token'), 'sha256');
		return (!$hash) ? $key : ($hash === $key);
	}

	public function client($key = 'agent')
	{
		return $this->get(['_client' => $key]);
	}
	
	public function hash($data = '', $algo = 'sha256')
	{
		return $this->load()->module('hash')->gen($data, $algo, $this->key());
	}

}
