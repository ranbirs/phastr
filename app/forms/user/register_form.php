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
			$params = array(
				'validate' => array(
					'maxlength' => 64
				)
			)
		);

		$this->field(array('input' => 'email'), "register_email", "Email",
			$params = array(
				'validate' => array(
					'maxlength' => 128,
					'email'
				)
			)
		);

		$this->field(array('input' => 'password'), "register_password", "Password",
			$params = array(
				'validate' => array(
					'maxlength' => 32,
					'required'
				)
			)
		);

		$this->field(array('button' => 'submit'), "register_submit", "Submit",
			$params = array('css' => array("btn", "btn-primary"))
		);

		$this->fail(\sys\utils\Vocab::t('register_fail', 'user'));
		$this->success(\sys\utils\Vocab::t('register_success', 'user'));
	}

	protected function resolve()
	{
		$user = new \app\models\User();

		$name = $this->request->field($this->fid(), 'register_name', $this->method());
		$email = $this->request->field($this->fid(), 'register_email', $this->method());
		$password = $this->request->field($this->fid(), 'register_password', $this->method());

		if ($user->register($name, $email, $password)) {
			return true;
		}
		return false;
	}

}
