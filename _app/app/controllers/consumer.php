<?php

namespace app\controllers;

class Consumer extends \sys\Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function request__post($params = [])
	{
		$url = 'http://' . $_SERVER['SERVER_NAME'] . '/index.php/provider/example-service/post-action';
		$oauth = [
			'consumer_key' => 'consumerx',
			'consumer_secret' => 'consumerx-secret',
			'token' => 'consumerx-token',
			'token_secret' => 'consumerx-token-secret'
		];

		$this->load()->module('aes', 'app');
		
		$data = ['foo' => 'bar'];

		$params['iv'] = $this->aes->iv();
		$params['data'] = $this->aes->encrypt($data, 'consumerx-token-secret', $params['iv']);

		$this->load()->module('oauth_consumer', 'app');
		$this->view->response = $this->oauth_consumer->request($url, $params, $oauth, 'post');
	}

	public function render()
	{
		print print_r($this->view->response, true);
	}

}