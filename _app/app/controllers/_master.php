<?php

namespace app\controllers;

abstract class _master extends \app\init\mvc\Controller
{

	public function init()
	{
		$this->loader()->init('sys/route');
		$this->loader()->init('sys/modules/view');
		
		$this->loader()->load('sys/modules/session');
		$this->loader()->load('sys/modules/request');
		$this->loader()->load('app/modules/config');
		$this->loader()->load('app/modules/access');
		
		if (key((array) $this->route->arg('ajax', true)) !== 1) {
			$this->session->render();
		}
		$this->view->uri = $this->route->uri();
		$this->view->lang = $this->session->client('lang');
		$this->view->app_title = 'Phastr (demo)';
		
		$this->view->title = '';
		$this->view->body = '';
		
		$this->loader($this->view)->load('sys/modules/assets');
		
		$this->view->assets->meta(['charset' => 'utf-8']);
		$this->view->assets->meta(['name' => 'author', 'content' => 'sourceforce.io']);
		$this->view->assets->meta(['name' => 'viewport', 'content' => 'width=device-width,initial-scale=1.0']);
		$this->view->assets->style('//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css', 'external');
		$this->view->assets->style('assets/css/style.css', 'file', ['media' => 'all']);
		
		$this->view->assets->script('//code.jquery.com/jquery-1.11.3.min.js', 'external');
		$this->view->assets->script('//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js', 'external');
		
		$this->loader()->load('app/navs/top_nav');
		$this->top_nav->get(null, $attr = ['class' => 'nav navbar-nav']);
		$this->view->top_nav = $this->top_nav->render();
		
		$this->loader()->load('app/navs/user_nav')->get(null, $attr = ['class' => ['nav', 'navbar-nav', 'pull-right']]);
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
