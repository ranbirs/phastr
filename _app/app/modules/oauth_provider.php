<?php

namespace app\modules;

use OAuthProvider;
use OAuthException;

class OAuth_provider
{
	
	use \sys\Loader;
	
	const method__ = OAUTH_HTTP_METHOD_GET;
	
	public $oauth;

	public function request($url, $params = null, $method = self::method__)
	{
		try {
			$this->oauth = new OAuthProvider();

			$this->oauth->consumerHandler(array($this, 'consumerHandler'));
			$this->oauth->timestampNonceHandler(array($this, 'timestampNonceHandler'));
			$this->oauth->tokenHandler(array($this, 'tokenHandler'));
			
			if (!empty($params)) {
				$url .= '?' . http_build_query($params);
			}
			$this->oauth->checkOAuthRequest($url, strtoupper($method));
		}
		catch (OAuthException $ex) {
			print OAuthProvider::reportProblem($ex);
			exit();
		}
		return ['request' => $_REQUEST, 'consumer' => $this->oauth->_consumer];
	}
	
	protected function consumer($key)
	{
		return $this->load()->module('conf', 'app')->ini('confs/server/consumers/' . $key);
	}

	public function consumerHandler($oauth)
	{
		$oauth->_consumer = $this->consumer($oauth->consumer_key);

		if (!isset($oauth->_consumer['consumer_key']) || !isset($oauth->_consumer['consumer_secret'])) {
			return OAUTH_CONSUMER_KEY_REFUSED;
		}
		if ($oauth->consumer_key !== $oauth->_consumer['consumer_key']) {
			return OAUTH_CONSUMER_KEY_REFUSED;
		}
		$oauth->consumer_secret = $oauth->_consumer['consumer_secret'];

		return OAUTH_OK;
	}
	
	public function timestampNonceHandler($oauth)
	{
		if (!$oauth->nonce || strlen($oauth->nonce) < 32) {
			return OAUTH_BAD_NONCE;
		}
		if (!$oauth->timestamp || strlen((int) $oauth->timestamp) < 10) {
			return OAUTH_BAD_TIMESTAMP;
		}
		return OAUTH_OK;
	}
	
	public function tokenHandler($oauth)
	{
		if (!isset($oauth->_consumer['token']) || !isset($oauth->_consumer['token_secret'])) {
			return OAUTH_TOKEN_REJECTED;
		}
		if ($oauth->token !== $oauth->_consumer['token']) {
			return OAUTH_TOKEN_REJECTED;
		}
		$oauth->token_secret = $oauth->_consumer['token_secret'];

		return OAUTH_OK;
	}

}