<?php

namespace app\controllers;

class Example_blog extends \sys\Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function init()
	{

	}

	public function scope()
	{
		$this->view->page = $this->view->page(\sys\Init::res('path'));
		$this->view->layout('index');
	}

}
