<?php

namespace app\controllers;

class Index extends _master
{

	public function index()
	{
	}

	public function private_page()
	{
		$this->access->permission('private');
	}

	public function example_forms()
	{
		$this->load()->load('app/forms/index/test_form');
		
		$this->test_form->imported_data = ['example' => ['data']];
		$this->test_form->get(['title' => 'Example form', 'attr' => ['class' => 'form form-horizontal']]);
		$this->view->test_form = $this->test_form->render('bootstrap');
		
		$this->load()->load('app/forms/index/simple_form');
		$this->simple_form->get(['title' => 'Simple form', 'attr' => ['class' => 'form form-horizontal']]);
		$this->view->simple_form = $this->simple_form->render();
		
		$this->load()->load('app/forms/index/service_form');
		$this->service_form->get(['title' => 'OAuth Consumer-Provider-AES-encryption form', 'attr' => ['class' => ['form', 'form-horizontal']]]);
		$this->view->service_form = $this->service_form->render();
		
		$this->view->request_method = $this->request->method;
		$this->view->request_path = \sys\utils\Path::uri($this->route->route('route', true) . '/ajax/request/request_example');
	}

}
