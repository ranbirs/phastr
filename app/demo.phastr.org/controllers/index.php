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
		$this->view->test_form = $this->form->test_form->html($data, $title = "An example form", $css = array("form-horizontal form-transform"));

		$this->load->form('index/simple_form');
		$this->view->simple_form = $this->form->simple_form->html($data = null, $title = "A simpler form", $css = array("form-inline"));

		$this->request->method = 'post';
		$this->request->layout = 'json';
	}

	protected function render()
	{
		$this->view->page = $this->view->page();
		$this->view->layout('index');
	}

}

