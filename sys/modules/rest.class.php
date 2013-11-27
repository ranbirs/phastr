<?php

namespace sys\modules;

use sys\Init;
use sys\utils\Helper;

class Rest {

	const cipher__ = \app\confs\rest\cipher__;
	const mode__ = \app\confs\rest\mode__;
	const rand__ = \app\confs\rest\rand__;
	const hash__ = \app\confs\rest\hash__;

	protected $token, $client, $params, $headers, $response, $body, $header, $info, $consumer;

	function construct()
	{
		if (!extension_loaded('mcrypt') or !extension_loaded('curl')) {
			Init::route()->error(404, "");
		}
	}

	public function init($url, $data = null, $params = [], $method = 'post')
	{
		if (filter_var($url, FILTER_VALIDATE_URL) === false) {
			return false;
		}
		$url = explode('?', $url, 2);
		(isset($url[1])) ? parse_str($url[1], $url[1]) : $url[1] = [];
		$this->params = $params;
		$this->token = hash(self::hash__, uniqid(microtime() . mt_rand(), true));

		if (isset($params['public']) and isset($params['passphrase'])) {
			$params['public'] = $this->publicKey($this->token, $params['public'], $params['passphrase']);
			$url[1]['_public'] = $params['public'];
		}
		if (isset($params['alias']))
			$url[1]['_alias'] = $params['alias'];

		$url[1] = http_build_query($url[1]);
		$url = $url[0] . ((strlen($url[1])) ? "?" . $url[1] : "");

		$this->client = curl_init($url);

		$data = $this->transfer($data, $params['private'], $params['public'], $this->token);

		$request[$method] = base64_encode($data);

		curl_setopt($this->client, CURLOPT_HEADER, true);
		curl_setopt($this->client, CURLOPT_RETURNTRANSFER, true);

		switch ($method) {
			case 'post':
				curl_setopt($this->client, CURLOPT_POST, true);
				curl_setopt($this->client, CURLOPT_POSTFIELDS, $request);
				break;
			case 'put':
				curl_setopt($this->client, CURLOPT_PUT, true);
				curl_setopt($this->client, CURLOPT_BINARYTRANSFER, true);
				break;
			case 'get':
			default:
				curl_setopt($this->client, CURLOPT_HTTPGET, true);
		}
	}

	public function client()
	{
		return $this->client;
	}

	public function setOpt($params = [])
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

	public function setHeader($headers = [], $client = true)
	{
		$headers = Helper::getStringArray(": ", $headers);

		if ($client) {
			if (!isset($this->headers))
				$this->headers = [];
			$this->headers = array_merge($this->headers, $headers);
			curl_setopt($this->client, CURLOPT_HTTPHEADER, $this->headers);
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

	public function transfer($data = null, $private = null, $public = null, $token = null, $client = true)
	{
		$data = serialize($data);

		if ($private and $public) {
			$vector = mcrypt_create_iv(mcrypt_get_iv_size(self::cipher__, self::mode__), self::rand__);
			$data = $this->encrypt($data, $private, $vector);
			$this->setHeader([$public => base64_encode($vector)], $client);
			$this->setHeader(['_token' => base64_encode($token)], $client);
		}
		return $data;
	}

	public function resolve($result = null, $params = [], $token = null, $vector = null)
	{
		$data = (is_null($result)) ? base64_decode($this->response()) : $result;
		if (empty($params))
			$params = $this->params;

		if (isset($params['private']) and isset($params['public'])) {
			if (is_null($vector)) {
				$public = $this->publicKey((is_null($token)) ? $this->token : $token, $params['public'], $params['passphrase']);
				$vector = base64_decode($this->getHeader($public));
			}
			$data = $this->decrypt($data, $params['private'], $vector);
		}
		return unserialize($data);
	}

	public function response()
	{
		if (!isset($this->response)) {
			$this->response = curl_exec($this->client);
			$this->info = curl_getinfo($this->client);

			$this->header = Helper::getArgs(Helper::getArray(eol__, trim(substr($this->response, 0, $header_size = (int) $this->getInfo('header_size')))));
			$this->body = trim(substr($this->response, $header_size));

			curl_close($this->client);
		}
		return $this->body;
	}

	public function publicKey($token, $public, $passphrase = "", $algo = \app\confs\rest\hash__)
	{
		return hash_hmac($algo, $public, hash($algo, $passphrase . $token));
	}

	public function privateKey($token, $public, $service, $alias, $host = null, $consumer = [])
	{
		if (empty($public) or empty($service) or empty($alias) or empty($consumer)) {
			return false;
		}
		if (!isset($consumer['private']) or !isset($consumer['public']) or !isset($consumer['service']) or !isset($consumer['alias'])) {
			return false;
		}
		if ($public !== $this->publicKey($token, $consumer['public'], $consumer['passphrase']) or $alias !== $consumer['alias']) {
			return false;
		}
		$consumer['service'] = (array) $consumer['service'];
		if (!in_array($service, $consumer['service'])) {
			return false;
		}
		if (isset($consumer['host'])) {
			$consumer['host'] = (array) $consumer['host'];
			if (empty($host) or !in_array($host, $consumer['host'])) {
				return false;
			}
		}
		return $consumer['private'];
	}

	public function encrypt($data = null, $private, $vector) {
		$private = hash(self::hash__, $private, true);
		$data = base64_encode(serialize($data));
		return mcrypt_encrypt(self::cipher__, $private, $data, self::mode__, $vector);
	}

	public function decrypt($data = null, $private, $vector) {
		$private = hash(self::hash__, $private, true);
		$data = mcrypt_decrypt(self::cipher__, $private, $data, self::mode__, $vector);
		return unserialize(base64_decode(trim($data)));
	}

}