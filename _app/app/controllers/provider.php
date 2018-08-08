<?php

namespace app\controllers;

class Provider extends \app\init\mvc\Controller
{

	protected $endpoint = '';

	public function init()
	{
		$this->loader()->init('sys/route');
		$this->loader()->init('sys/modules/view');
		
		$this->endpoint = 'http://' . $_SERVER['SERVER_NAME'] . \sys\utils\Path::uri($this->route->route('route', true));
	}

	public function example_service_get_action($params = [])
	{
		$this->loader()->load('app/modules/oauth_provider');
		if ($request = $this->oauth_provider->request($this->endpoint, $_GET, 'get')) {
			$response = $this->loader()->load('app/services/example_service')->post_action($request['request'], $request['consumer']);
			return $this->view->response = $response;
		}
		http_response_code(401);
		exit();
	}

	public function example_service_post_action($params = [])
	{
		$this->loader()->load('app/modules/oauth_provider');
		if ($request = $this->oauth_provider->request($this->endpoint, $_GET, 'post')) {
			$response = $this->loader()->load('app/services/example_service')->post_action($request['request'], $request['consumer']);
			return $this->view->response = $response;
		}
		http_response_code(401);
		exit();
	}

	public function render()
	{
		$this->view->layout('app/views/layouts/request/json');
	}

}
