<?php

namespace sys\modules;

use sys\Init;

class Rest {

	const algo__ = MCRYPT_RIJNDAEL_256;
	const mode__ = MCRYPT_MODE_CBC;
	const rand__ = MCRYPT_DEV_URANDOM;
	const hash__ = 'sha256';

	protected $client, $result;

	function __construct()
	{
		if (!extension_loaded('mcrypt') or !extension_loaded('curl')) {
			Init::view()->error(404, "");
		}
	}

	public function init($url, $data = null, $method = 'post', $private = null)
	{
		$this->client = curl_init($url);

		switch ($method) {
			case 'post':
				curl_setopt($this->client, CURLOPT_POST, true);
				break;
			case 'put':
				//curl_setopt($this->client, CURLOPT_PUT, true);
				//break;
			case 'get':
			default:
				curl_setopt($this->client, CURLOPT_HTTPGET, true);
		}
		if ($private) {
			$vector = mcrypt_create_iv(mcrypt_get_iv_size(self::algo__, self::mode__), self::rand__);
			$data = $this->encrypt($data, $private, $vector);
		}
		$data = array('fields' => base64_encode($data), 'vector' => base64_encode($vector));
		curl_setopt($this->client, CURLOPT_POSTFIELDS, $data);
	}

	public function client()
	{
		return $this->client;
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

	public function setHeader($headers = array())
	{
		curl_setopt($this->client, CURLOPT_HTTPHEADER, $headers);
	}

	public function result()
	{
		if (!isset($this->result)) {
			curl_setopt($this->client, CURLOPT_RETURNTRANSFER, true);
			$this->result = curl_exec($this->client);
			curl_close($this->client);
		}
		return $this->result;
	}

	public function response($format = 'json')
	{
		$response = $this->result();
		switch ($format) {
			case 'json':
				$response = json_decode($response);
				break;
		}
		return $response;
	}

	public function resolve($fields = null, $vector = null, $private = null)
	{
		$fields = base64_decode($fields);
		return ($private) ? $this->decrypt($fields, $private, base64_decode($vector)) : $fields;
	}

	public function respond($data = null, $private = null)
	{
		$vector = null;
		if ($private) {
			$vector = mcrypt_create_iv(mcrypt_get_iv_size(self::algo__, self::mode__), self::rand__);
			$data = $this->encrypt($data, $private, $vector);
		}
		return array('result' => base64_encode($data), 'vector' => base64_encode($vector));
	}

	public function privateKey($service, $alias, $public, $host = null, $consumer = null)
	{
		if (empty($service) or empty($alias) or empty($public) or empty($consumer)) {
			return false;
		}
		if (!isset($consumer['service']) or !isset($consumer['alias']) or !isset($consumer['public']) or !isset($consumer['private'])) {
			return false;
		}
		if ($consumer['alias'] !== $alias or $consumer['public'] !== $public) {
			return false;
		}
		if (!is_array($consumer['service']))
			$consumer['service'] = array($consumer['service']);
		if (!in_array($service, $consumer['service'])) {
			return false;
		}
		if (isset($consumer['host'])) {
			if (!is_array($consumer['host']))
				$consumer['host'] = array($consumer['host']);
			if (empty($host) or !in_array($host, $consumer['host'])) {
				return false;
			}
		}
		return $consumer['private'];
	}

	public function encrypt($data = null, $private, $vector) {
		$private = hash(self::hash__, $private, true);
		$data = base64_encode(serialize($data));
		return mcrypt_encrypt(self::algo__, $private, $data, self::mode__, $vector);
	}

	public function decrypt($data = null, $private, $vector) {
		$private = hash(self::hash__, $private, true);
		$data = mcrypt_decrypt(self::algo__, $private, $data, self::mode__, $vector);
		return unserialize(base64_decode(trim($data)));
	}

} 
