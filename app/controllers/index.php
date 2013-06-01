<?php

namespace app\controllers;

class Index extends \sys\Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function init()
	{

	}

	public function example_forms_index()
	{
		$this->load->form('index/test_form');
		$data = array('example' => array("data"));
		$this->view->test_form = $this->test_form->html($data, $title = "Example Form", array("form-horizontal"));
	}

	public function scope()
	{
		$this->view->page = $this->view->page();
		$this->view->layout('index');
	}

}

