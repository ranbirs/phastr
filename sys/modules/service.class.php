<?php

namespace sys\modules;

class Service {

	const algo__ = MCRYPT_RIJNDAEL_256;
	const mode__ = MCRYPT_MODE_CBC;
	const rand__ = MCRYPT_DEV_URANDOM;

	protected $client, $result, $vector;

	function __construct()
	{

	}

	public function request($url, $key = "pubkey1", $data = null, $method = 'post')
	{
		$this->client = curl_init($url); //@

		$keys['pubkey1'] = "super-secret";

		switch ($method) {
			case 'post':
				curl_setopt($this->client, CURLOPT_POST, true);
				break;
			case 'put':
				curl_setopt($this->client, CURLOPT_PUT, true);
				break;
			case 'get':
			default:
				curl_setopt($this->client, CURLOPT_HTTPGET, true);

		}
		$ivs = mcrypt_get_iv_size(self::algo__, self::mode__);
		$this->vector = mcrypt_create_iv($ivs, self::rand__);

		$submit = array(
			'content' => base64_encode($this->encrypt($keys[$key], $data, $this->vector)),
			'request' => base64_encode($this->vector)
		);
		curl_setopt($this->client, CURLOPT_POSTFIELDS, $submit);

		return $this->vector;
	}

	public function encrypt($key, $data, $vector) {
		$key = hash('sha256', $key, true);
		$data = base64_encode(serialize($data));
		return mcrypt_encrypt(self::algo__, $key, $data, self::mode__, $vector);
	}

	public function decrypt($key, $data, $vector) {
		$key = hash('sha256', $key, true);
		$data = mcrypt_decrypt(self::algo__, $key, $data, self::mode__, $vector);
		return unserialize(base64_decode(trim($data)));
	}


	public function setOpt($params = array())
	{
		foreach ($params as $args) {
			if (!is_array($args)) {
				curl_setopt($this->client, $args);
			}
			else {
				if (isset($args[1])) {
					curl_setopt($this->client, $args[0], $args[1]);
				}
				else {
					curl_setopt($this->client, $args[0]);
				}
			}
		}
	}

	public function client()
	{
		return $this->client;
	}

	public function result($type = null)
	{
		if (!isset($this->result)) {
			//curl_setopt($this->client, CURLOPT_HTTPHEADER, array("Content-Type: $type"));
			curl_setopt($this->client, CURLOPT_RETURNTRANSFER, true);
			$this->result = curl_exec($this->client);
			curl_close($this->client);
		}
		return $this->result;
	}

} 
