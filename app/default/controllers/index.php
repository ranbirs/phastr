<?php

namespace app\controllers;

class Index extends \sys\Controller {

	function __construct()
	{
		parent::__construct();
	}

	protected function index_index()
	{

		$this->load->form('index/simple_form');
		$data = "imported data...";
		$this->view->simple_form = $this->form->simple_form->html($data, $title = "Example form", $css = array("form-horizontal"));

	}

	protected function render()
	{
		$this->view->page = $this->view->page();
		$this->view->layout('index');
	}

}

