<?php

namespace app\forms\user;

class Login_form extends \sys\modules\Form {

	function __construct()
	{
		parent::__construct();
	}

	protected function build($import = null)
	{
		$this->field(array('input' => 'email'), "login_email", "Email",
			$params = array(
				'validate' => array(
					'maxlength' => 128,
					'email'
				)
			)
		);

		$this->field(array('input' => 'password'), "login_password", "Password",
			$params = array(
				'validate' => array(
					'maxlength' => 32,
					'required'
				)
			)
		);

		$this->field(array('button' => 'submit'), "login_submit", "Sign in",
			$params = array('css' => array("btn", "btn-primary"))
		);

		$this->success("You have successfully signed in", array('callback' => "location.href = '/user/'"));
		$this->fail("Invalid credentials");
	}

	protected function resolve($submit = null, $import = null)
	{
		$user = new \app\models\User();

		if ($user->login($submit['login_email'], $submit['login_password'])) {
			return true;
		}
		return false;
	}

}
