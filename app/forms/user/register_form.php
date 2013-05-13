<?php

namespace app\forms\user;

class Register_form extends \sys\modules\Form {

	function __construct()
	{
		parent::__construct();
	}

	protected function build()
	{
		$this->open("Registration Form", array("form-horizontal"));

		$this->field(array('input' => 'text'), "register_name", "Name",
			$data = array(
				'validate' => array(
					'maxlength' => array('value' => 64)
				)
			)
		);

		$this->field(array('input' => 'email'), "register_email", "Email",
			$data = array(
				'validate' => array(
					'maxlength' => array('value' => 128),
					'email' => ""
				)
			)
		);

		$this->field(array('input' => 'password'), "register_password", "Password",
			$data = array(
				'validate' => array(
					'maxlength' => array('value' => 32),
					'required' => ""
				)
			)
		);

		$this->field(array('button' => 'submit'), "register_submit", "Submit",
			$data = array('css' => array("btn", "btn-primary"))
		);

		$this->close();
	}

	protected function parse()
	{
		$user = new \app\models\User();

		$name = $this->xhr->post('register_name');
		$email = $this->xhr->post('register_email');
		$password = $this->xhr->post('register_password');

		$register = $user->register($name, $email, $password);
		if ($register) {
			return array('output' => true);
		}
		return array('output' => false, 'message' => \sys\utils\Vocab::t('user_register\\fail'));
	}

	protected function success()
	{
		$msg = \sys\utils\Vocab::t('user_register\\success');
		return array('message' => $msg, 'callback' => "");
	}

}
