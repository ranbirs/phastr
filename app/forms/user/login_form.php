<?php

namespace app\forms\user;

class Login_form extends \sys\modules\Form {

	function __construct()
	{
		parent::__construct();
	}

	protected function build($data = null)
	{
		$this->field(array('input' => 'email'), "login_email", "Email",
			$build = array(
				'validate' => array(
					'maxlength' => array('value' => 128),
					'email' => ""
				)
			)
		);

		$this->field(array('input' => 'password'), "login_password", "Password",
			$build = array(
				'validate' => array(
					'maxlength' => array('value' => 32),
					'required' => ""
				)
			)
		);

		$this->field(array('button' => 'submit'), "login_submit", "Sign in",
			$build = array('css' => array("btn", "btn-primary"))
		);
	}

	protected function parse()
	{
		$user = new \app\models\User();
		$email = $this->xhr->post('login_email');
		$password = $this->xhr->post('login_password');

		if ($user->login($email, $password)) {
			return array('result' => true);
		}
		return array('result' => false, 'message' => "Invalid credentials");
	}

	protected function success()
	{
		return array('message' => "Successfully logged in", 'callback' => "location.href = '/user/'");
	}

}
