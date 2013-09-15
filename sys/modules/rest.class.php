<?php

namespace sys\modules;

use sys\Init;
use sys\utils\Helper;

class Rest {

	private static $algo = \app\confs\rest\algo__;
	private static $mode = \app\confs\rest\mode__;
	private static $rand = \app\confs\rest\rand__;
	private static $hash = \app\confs\rest\hash__;

	protected $client, $result, $header, $info;

	function construct()
	{
		if (!extension_loaded('mcrypt') or !extension_loaded('curl')) {
			Init::view()->error(404, "");
		}
	}

	public function init($url, $data = null, $method = 'post', $private = null)
	{
		$this->client = curl_init($url);
		$data = serialize($data);
		$request = array();

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
		if ($private) {
			$vector = mcrypt_create_iv(mcrypt_get_iv_size(self::$algo, self::$mode), self::$rand);
			$data = $this->encrypt($data, $private, $vector);
			$this->setHeader(array('_vector: ' . base64_encode($vector)));
		}
		$request['body'] = base64_encode($data);

		curl_setopt($this->client, CURLOPT_HEADER, true);
		curl_setopt($this->client, CURLOPT_VERBOSE, true);
		curl_setopt($this->client, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->client, CURLOPT_POSTFIELDS, $request);
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

	public function setHeader($headers, $client = true)
	{
		if (!is_array($headers))
			$headers = array($headers);

		if ($client) {
			curl_setopt($this->client, CURLOPT_HTTPHEADER, $headers);
		}
		else {
			foreach ($headers as $header)
				header($header);
		}
	}

	public function getHeader($key = null, $client = true)
	{
		if ($client) {
			return (!is_null($key)) ? ((isset($this->header[$key])) ? $this->header[$key] : null) : $this->header;
		}
		else {
			return Init::request()->header($key);
		}
	}

	public function getInfo($key = null)
	{
		return (!is_null($key)) ? ((isset($this->info[$key])) ? $this->info[$key] : null) : $this->info;
	}

	public function result()
	{
		if (!isset($this->result)) {
			$result = curl_exec($this->client);
			$this->info = curl_getinfo($this->client);

			$header_size = (int) $this->getInfo('header_size');
			$this->header = array_filter(Helper::getArgs(explode("\n", trim(substr($result, 0, $header_size)))));
			$this->result = trim(substr($result, $header_size));

			curl_close($this->client);
		}
		return $this->result;
	}

	public function response($format = 'json')
	{
		$result = $this->result();
		switch ($format) {
			case 'json':
				$result = json_decode($result);
				break;
		}
		return $result;
	}

	public function respond($data = null, $private = null)
	{
		$data = serialize($data);
		$response = array();
		if ($private) {
			$vector = mcrypt_create_iv(mcrypt_get_iv_size(self::$algo, self::$mode), self::$rand);
			$data = $this->encrypt($data, $private, $vector);
			$response['vector'] = base64_encode($vector);
		}
		$response['result'] = base64_encode($data);
		return $response;
	}

	public function resolve($result = null, $private = null, $vector = null)
	{
		$data = base64_decode($result);
		if ($private) {
			if (is_null($vector))
				$vector = $this->getHeader('_vector');
			$data = $this->decrypt($data, $private, base64_decode($vector));
		}
		return unserialize($data);
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
		$private = hash(self::$hash, $private, true);
		$data = base64_encode(serialize($data));
		return mcrypt_encrypt(self::$algo, $private, $data, self::$mode, $vector);
	}

	public function decrypt($data = null, $private, $vector) {
		$private = hash(self::$hash, $private, true);
		$data = mcrypt_decrypt(self::$algo, $private, $data, self::$mode, $vector);
		return unserialize(base64_decode(trim($data)));
	}

} 
