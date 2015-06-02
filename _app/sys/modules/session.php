<?php

namespace sys\modules;

use sys\Loader;
use sys\configs\Session as __session;

class Session extends \sys\components\Session
{
	
	use Loader;

	function __construct()
	{
		$this->start((__session::database__) ? new \sys\handlers\Session() : null);
	}

	public function generate()
	{
		$this->set(['_timestamp' => 0], $this->timestamp(true));
		$this->set('_token', $this->token(true));
		$this->set('_key', $this->keygen());
		$this->set(['_client' => 'lang'], __session::lang__);
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

	public function token($gen = false, $algo = 'sha1')
	{
		if (!$gen) {
			return $this->get('_token');
		}
		return $this->loader()->load('sys/modules/hash')->gen($this->session_id, $algo, $this->timestamp()[0]);
	}

	public function key()
	{
		return $this->get('_key');
	}

	public function keygen($hash = null, $algo = __session::algo__)
	{
		$key = $this->loader()->load('sys/modules/hash')->gen($this->get('_token'), $algo, __session::key__);
		return (!isset($hash)) ? $key : ($hash === $key);
	}

	public function client($key = 'agent')
	{
		return $this->get(['_client' => $key]);
	}

	public function hash($data = null, $algo = __session::algo__)
	{
		return $this->loader()->load('sys/modules/hash')->gen($data, $algo, $this->key());
	}

}
