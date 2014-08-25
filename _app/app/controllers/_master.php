<?php

namespace app\controllers;

abstract class _master extends \sys\Controller
{

	public function init()
	{
		$this->load()->init('route');
		$this->load()->init('view');

		$this->load()->module('session');
		$this->load()->module('request');
		$this->load()->module('config', 'app');
		$this->load()->module('access', 'app');
		
		if (!$this->route->request()) {
			$this->session->render();
		}
		$this->view->uri = $this->route->uri();
		$this->view->lang = $this->session->client('lang');
		$this->view->app_title = 'Phastr (demo)';
		
		$this->view->title = '';
		$this->view->body = '';
		
		$this->load($this->view)->module('assets');
		
		$this->view->assets->meta(['charset' => 'utf-8']);
		$this->view->assets->meta(['name' => 'author', 'content' => 'sourceforce.io']);
		$this->view->assets->meta(['name' => 'viewport', 'content' => 'width=device-width,initial-scale=1.0']);
		$this->view->assets->style('assets/lib/bootstrap/css/bootstrap.min.css', 'file', null, '3.1.1');
		$this->view->assets->style('assets/css/style.css', 'file', ['media' => 'all']);
		
		$this->view->assets->script('assets/lib/jquery/jquery.min.js', 'file', null, '1.11.0');
		$this->view->assets->script('assets/lib/bootstrap/js/bootstrap.min.js', 'file', null, '3.1.1');
		
		$this->load()->nav('top_nav');
		$this->top_nav->get(null, $attr = ['class' => 'nav navbar-nav']);
		$this->view->top_nav = $this->top_nav->render();

		$this->load()->nav('user_nav')->get(null, $attr = ['class' => ['nav', 'navbar-nav', 'pull-right']]);
		$this->view->user_nav = $this->user_nav->render();
	}

	public function render()
	{
		if ($request_subj = $this->route->request()) {
			$this->view->response = $this->request->resolve($this, $request_subj);
			$this->view->request = $this->request->request();
			$this->view->layout('request/' . $this->request->format);
		}
		$this->view->assets->script('$.ajaxSetup({headers: {\'' . $this->session->token() . '\': \'' . $this->session->get('_request') . '\'}});', 'inline');
		$this->view->page = $this->view->page();
		$this->view->layout();
	}

}
