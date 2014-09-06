<?php

namespace app\controllers;

class Provider extends \sys\Controller
{

    protected $endpoint = '';

    public function init()
    {
        $this->load()->init('route');
        $this->load()->init('view');

        $this->endpoint = 'http://' . $_SERVER['SERVER_NAME'] . \sys\utils\Path::uri($this->route->path('route'));
    }

    public function example_service_get_action($params = [])
    {
        $this->load()->module('oauth_provider', 'app');
        if ($request = $this->oauth_provider->request($this->endpoint, $_GET, 'get')) {
            $response = $this->load()->service('example_service')->post_action($request['request'], $request['consumer']);
            return $this->view->response = $response;
        }
        http_response_code(401);
        exit();
    }

    public function example_service_post_action($params = [])
    {
        $this->load()->module('oauth_provider', 'app');
        if ($request = $this->oauth_provider->request($this->endpoint, $_GET, 'post')) {
            $response = $this->load()->service('example_service')->post_action($request['request'], $request['consumer']);
            return $this->view->response = $response;
        }
        http_response_code(401);
        exit();
    }

    public function render()
    {
        $this->view->layout('request/json');
    }

}

 
