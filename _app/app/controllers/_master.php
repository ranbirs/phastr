<?php

namespace app\controllers;

abstract class _master extends \app\init\mvc\Controller
{

	public function init()
	{
		$this->load()->init('sys/route');
		$this->load()->init('sys/view');
		
		$this->load()->load('sys/modules/session');
		$this->load()->load('sys/modules/request');
		$this->load()->load('app/modules/config');
		$this->load()->load('app/modules/access');
		
		if (key((array) $this->route->arg('ajax', true)) !== 1) {
			$this->session->render();
		}
		$this->view->uri = $this->route->uri();
		$this->view->lang = $this->session->client('lang');
		$this->view->app_title = 'Phastr (demo)';
		
		$this->view->title = '';
		$this->view->body = '';
		
		$this->load($this->view)->load('sys/modules/assets');
		
		$this->view->assets->meta(['charset' => 'utf-8']);
		$this->view->assets->meta(['name' => 'author', 'content' => 'sourceforce.io']);
		$this->view->assets->meta(['name' => 'viewport', 'content' => 'width=device-width,initial-scale=1.0']);
		$this->view->assets->style('assets/lib/bootstrap/css/bootstrap.min.css', 'file', null, '3.1.1');
		$this->view->assets->style('assets/css/style.css', 'file', ['media' => 'all']);
		
		$this->view->assets->script('assets/lib/jquery/jquery.min.js', 'file', null, '1.11.0');
		$this->view->assets->script('assets/lib/bootstrap/js/bootstrap.min.js', 'file', null, '3.1.1');
		
		$this->load()->load('app/navs/top_nav');
		$this->top_nav->get(null, $attr = ['class' => 'nav navbar-nav']);
		$this->view->top_nav = $this->top_nav->render();
		
		$this->load()->load('app/navs/user_nav')->get(null, $attr = ['class' => ['nav', 'navbar-nav', 'pull-right']]);
		$this->view->user_nav = $this->user_nav->render();
	}

	public function render()
	{
		if ($request_subj = $this->route->arg('ajax')) {
			$this->view->response = $this->request->resolve($this, $request_subj);
			$this->view->request = $this->request->request();
			$this->view->layout('app/views/layouts/request/' . $this->request->format);
		}
		$this->view->assets->script('$.ajaxSetup({headers: {\'' . $this->session->token() . '\': \'' . $this->session->get('_request') . '\'}});', 'inline');
		$this->view->page = $this->view->view('app/views/pages/' . $this->route->resource() . '/' . $this->route->action('-', '_'));
		$this->view->layout('app/views/layouts/default');
	}

}
