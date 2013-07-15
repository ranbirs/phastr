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
					'email'
				)
			)
		);

		$this->field(array('input' => 'password'), "login_password", "Password",
			$build = array(
				'validate' => array(
					'maxlength' => array('value' => 32),
					'required'
				)
			)
		);

		$this->field(array('button' => 'submit'), "login_submit", "Sign in",
			$build = array('css' => array("btn", "btn-primary"))
		);

		$tihs->success("<p>You have successfully signed in.</p>", 'callback' => "location.href = '/user/'");
		$tihs->fail("Invalid credentials");
	}

	protected function resolve()
	{
		$user = new \app\models\User();

		$email = $this->xhr->context($this->fid, 'login_email', $this->method);
		$password = $this->xhr->context($this->fid, 'login_password', $this->method);

		if ($user->login($email, $password)) {
			return true;
		}
		return false;
	}

}
