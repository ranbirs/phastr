<?php

namespace app\forms\user;

class Register_form extends \sys\modules\Form {

	function __construct()
	{
		parent::__construct();
	}

	protected function build($data = null)
	{
		$this->field(array('input' => 'text'), "register_name", "Name",
			$build = array(
				'validate' => array(
					'maxlength' => array('value' => 64)
				)
			)
		);

		$this->field(array('input' => 'email'), "register_email", "Email",
			$build = array(
				'validate' => array(
					'maxlength' => array('value' => 128),
					'email' => ""
				)
			)
		);

		$this->field(array('input' => 'password'), "register_password", "Password",
			$build = array(
				'validate' => array(
					'maxlength' => array('value' => 32),
					'required' => ""
				)
			)
		);

		$this->field(array('button' => 'submit'), "register_submit", "Submit",
			$build = array('css' => array("btn", "btn-primary"))
		);
	}

	protected function parse()
	{
		$user = new \app\models\User();
		$name = $this->xhr->post('register_name');
		$email = $this->xhr->post('register_email');
		$password = $this->xhr->post('register_password');

		if ($user->register($name, $email, $password)) {
			return array('result' => true);
		}
		return array('result' => false, 'message' => \sys\utils\Vocab::t('user\\register_fail'));
	}

	protected function success()
	{
		$msg = \sys\utils\Vocab::t('user\\register_success');
		return array('message' => $msg, 'callback' => "");
	}

}
