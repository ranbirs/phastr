<?php

namespace app\controllers;

class Xhr extends \sys\Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function init()
	{
		if (!$this->xhr->token()) {
			$this->view->error(404);
		}
	}

	public function post_scope($action)
	{
		$this->_post($action);
	}

	public function get_scope($action)
	{
		$this->_get($action);
	}

	private function _post($action)
	{
		$this->_response('post', $action);
	}

	private function _get($action)
	{
		$this->_response('get', $action);
	}

	private function _response($request, $action)
	{
		$format = \sys\Init::res('args', 'format');
		if (!$format)
			$format = 'json';
		$this->view->request = $this->xhr->$request();
		$data = $this->view->page("xhr/$request/$action");
		$this->xhr->response($data, $format);
	}

}
