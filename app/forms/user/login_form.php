<?php

namespace app\forms\user;

class Login_form extends \sys\modules\Form {

	function __construct()
	{
		parent::__construct();
	}

	protected function build()
	{
		$this->open("Authentication Form", array("form-horizontal"));

		$this->field(
			$field = array('input' => 'email'), "login_email", "Email",
			$data = array(
				'validate' => array(
					'maxlength' => array('value' => 128),
					'email' => ""
				)
			)
		);

		$this->field(
			$field = array('input' => 'password'), "login_password", "Password",
			$data = array(
				'validate' => array(
					'maxlength' => array('value' => 32),
					'required' => ""
				)
			)
		);

		$this->field(array('button' => 'submit'), "login_submit", "Sign in",
			$data = array('css' => array("btn", "btn-primary"))
		);

		$this->close();
	}

	protected function parse()
	{
		$user = new \app\models\User();
		$email = $this->xhr->post('login_email');
		$password = $this->xhr->post('login_password');

		if ($user->login($email, $password)) {
			return array('output' => true);
		}
		return array('output' => false, 'message' => "Invalid credentials");
	}

	protected function success()
	{
		return array('message' => "Successfully logged in", 'callback' => "location.href = '/user/'");
	}

}
