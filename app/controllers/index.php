<?php

namespace app\controllers;

class Index extends \sys\Controller {

	function __construct()
	{
		parent::__construct();
	}

	protected function index_index()
	{

	}

	protected function multi_level__sub_level__example_page_index()
	{

	}

	protected function example_forms_index()
	{
		$this->load->form('index/test_form');
		$data = array('example' => array("data"));
		$this->view->test_form = $this->test_form->html($data, $title = "Example Form", $css = array("form-horizontal"));
	}

	protected function render()
	{
		$this->view->page = $this->view->page();
		$this->view->layout('index');
	}

}

