<?php

namespace app\controllers;

class _scope extends \sys\components\Constructor {

	function __construct()
	{
		parent::__construct();

		\sys\Load::vocab('lexicon');

		$this->view->assets->set('meta', "viewport", "width=device-width, initial-scale=1.0");
		$this->view->assets->set('style', "css/bootstrap.min.css", array('media' => "all"), "3.0.0");
		//$this->view->assets->set('style', "css/bootstrap-responsive.min.css", array('media' => "screen"), "2.3.0");
		$this->view->assets->set('style', "css/style.css", array('media' => "all"));

		$this->view->assets->set('script', "js/jquery.min.js", null, "1.10.2");
		$this->view->assets->set('script', "js/bootstrap.min.js", null, "3.0.0");

		$this->load->nav('top_nav');
		$this->view->top_nav = $this->nav->top_nav->html($data = null, $title = null, $css = array("nav navbar-nav"));

		$this->load->nav('user_nav');
		$this->view->user_nav = $this->nav->user_nav->html($data = null, $title = null, $css = array("nav navbar-nav", "pull-right"));

		$this->view->callback = "";
		$this->view->app_title = \sys\utils\Conf::k('title');

		$this->load->model('node');
		$node = $this->model->node->data(array('title', 'body'), \sys\Init::route()->path());

		$this->view->title = (isset($node->title)) ? $node->title : "";
		$this->view->body = (isset($node->body)) ? $node->body : "";
	}

}
