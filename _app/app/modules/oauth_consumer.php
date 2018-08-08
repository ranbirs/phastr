<?php

namespace app\modules;

use OAuth;
use OAuthException;

class OAuth_consumer
{

	const method__ = OAUTH_HTTP_METHOD_GET;

	const sig__ = OAUTH_SIG_METHOD_HMACSHA256;

	const type__ = OAUTH_AUTH_TYPE_URI;

	public $oauth, $info, $response;

	public function request($url, $params, $oauth, $method = self::method__, $sig = self::sig__, $type = self::type__)
	{
		try {
			$this->oauth = new OAuth($oauth['consumer_key'], $oauth['consumer_secret'], $sig, $type);
			
			$this->oauth->setToken($oauth['token'], $oauth['token_secret']);
			$this->oauth->setNonce(hash('sha1', uniqid(mt_rand(), true)));
			$this->oauth->setTimestamp(microtime(true));
			
			$this->oauth->fetch($url, $params, strtoupper($method));
			
			$this->info = $this->oauth->getLastResponseInfo();
			$this->response = $this->oauth->getLastResponse();
		} catch (OAuthException $ex) {
			print $ex->lastResponse;
			exit();
		}
		return $this->response;
	}

}