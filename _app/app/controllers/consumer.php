<?php

namespace app\controllers;

class Consumer extends \app\init\mvc\Controller
{

	public function init()
	{
		$this->loader()->init('sys/modules/view');
	}

	public function request($params = [])
	{
		$url = 'http://' . $_SERVER['SERVER_NAME'] . '/index.php/provider/example-service/post-action';
		$oauth = [
			'consumer_key' => 'consumerx', 
			'consumer_secret' => 'consumerx-secret', 
			'token' => 'consumerx-token', 
			'token_secret' => 'consumerx-token-secret'];
		
		$this->loader()->load('app/modules/aes');
		
		$data = ['foo' => 'bar'];
		
		$params['iv'] = $this->aes->iv();
		$params['data'] = $this->aes->encrypt($data, 'consumerx-token-secret', $params['iv']);
		
		$this->loader()->load('app/modules/oauth_consumer');
		$this->view->response = $this->oauth_consumer->request($url, $params, $oauth, 'post');
	}

	public function render()
	{
		print print_r($this->view->response, true);
	}

}