<?php

namespace app\controllers;

class _scope extends \sys\Controller {

	function __construct()
	{
		parent::__construct();

		\sys\Load::vocab('lexicon');

		$this->view->assets('meta', "viewport", "width=device-width, initial-scale=1.0");
		$this->view->assets('style', "/css/bootstrap-responsive.min.css?2.3.0", array('media' => "screen"), null);

		$this->load->nav('top_nav');
		$this->view->top_nav = $this->top_nav->html();

		$this->load->nav('user_nav');
		$this->view->user_nav = $this->user_nav->html();

		$this->view->callback = "";
		$this->view->app_title = \sys\utils\Conf::k('app\\title');

		$this->load->model('node');
		$node = $this->node->data(array('title', 'body'), \sys\Init::res('path'));

		$this->view->title = (isset($node->title)) ? $node->title : "";
		$this->view->body = (isset($node->body)) ? $node->body : "";
	}

}
