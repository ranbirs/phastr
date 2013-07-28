<?php

namespace app\controllers;

class User extends \sys\Controller {

	function __construct()
	{
		parent::__construct();

		$this->view->title = "User";
		\sys\Load::vocab('user', 'en');
	}

	protected function index_index()
	{

	}

	protected function login_index()
	{
		$this->load->form('user/login_form');
		$this->view->login_form = $this->form->login_form->html($data = null, $title = "Authentication form", $css = array("form-horizontal"));
	}

	protected function register_index()
	{
		$this->load->form('user/register_form');
		$this->view->title = "New User Registration";
		$this->view->body = $this->form->register_form->html($data = null, $title = "Registration form", $css = array("form-horizontal"));
	}

	protected function register_verify()
	{
		$token = \sys\Init::route()->args(1);
		if (\sys\Init::route()->args(0) !== $this->session->xid()) {
			$this->view->error(404);
		}
		if ($token) {
			if ($this->user->verify($token)) {
				$this->view->title = "New User Verification";
				$this->view->body = $this->view->page('user/register/verify');
				return true;
			}
		}
		$this->view->error(404);
	}

	protected function logout_index()
	{
		if ($this->session->token())
			$this->session->drop('_user');
	}

	protected function render()
	{
		$this->view->page = $this->view->page();
		$this->view->layout('index');
	}

}
