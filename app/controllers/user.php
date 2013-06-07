<?php

namespace app\controllers;

class User extends \sys\Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function init()
	{
		$this->load->model('user');
		$this->view->title = "User";
	}

	public function login_index()
	{
		$this->load->form('user/login_form');
		$this->view->login_form = $this->login_form->html($data = null, $title = "Authentication Form", $css = array("form-horizontal"));
	}

	public function register_index()
	{
		$this->load->form('user/register_form');
		$this->view->title = "New User Registration";
		$this->view->body = $this->register_form->html($data = null, $title = "Registration Form", $css = array("form-horizontal"));
	}

	public function register_verify()
	{
		$token = \sys\Res::get('params', 1);
		if (\sys\Res::get('params', 0) !== \sys\Res::session()->xid()) {
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

	public function logout_index()
	{
		if (\sys\Res::session()->token()) {
			\sys\Res::session()->drop('_user');
		}
	}

	public function scope()
	{
		$this->view->page = $this->view->page();
		$this->view->layout('index');
	}

}
