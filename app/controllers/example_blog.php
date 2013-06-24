<?php

namespace app\controllers;

class Example_blog extends \sys\Controller {

	function __construct()
	{
		parent::__construct();
	}

	protected function test_index()
	{

	}

	protected function test1_index()
	{

	}

	protected function render()
	{
		$this->view->page = $this->view->page(\sys\Res::get('path'));
		$this->view->layout('index');
	}

}
