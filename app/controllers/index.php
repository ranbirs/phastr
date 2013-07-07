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
		$this->view->test_form = $this->form->test_form->html($data, $title = "An example form", $css = array("form-horizontal"));

		$this->load->form('index/simple_form');
		$this->view->simple_form = $this->form->simple_form->html($data = null, $title = "A simpler form", $css = array("form-inline"));

<<<<<<< HEAD
		$this->view->xhr_method = 'post';
		$this->view->xhr_layout = 'json';
=======
		$this->view->request_method = 'post';
		$this->view->request_format = 'json';
>>>>>>> d6a96e0a4e6f64cabab2fc6a9729eb94aa71ea4b
	}

	protected function render()
	{
		$this->view->page = $this->view->page();
		$this->view->layout('index');
	}

}

