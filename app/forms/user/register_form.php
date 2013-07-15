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
					'email'
				)
			)
		);

		$this->field(array('input' => 'password'), "register_password", "Password",
			$build = array(
				'validate' => array(
					'maxlength' => array('value' => 32),
					'required'
				)
			)
		);

		$this->field(array('button' => 'submit'), "register_submit", "Submit",
			$build = array('css' => array("btn", "btn-primary"))
		);

		$this->fail(\sys\utils\Vocab::t('user\\register_fail'));
		$this->success(\sys\utils\Vocab::t('user\\register_success'));
	}

	protected function resolve()
	{
		$user = new \app\models\User();

		$name = $this->xhr->context($this->fid, 'register_name', $this->method);
		$email = $this->xhr->context($this->fid, 'register_email', $this->method);
		$password = $this->xhr->context($this->fid, 'register_password', $this->method);

		if ($user->register($name, $email, $password)) {
			return true;
		}
		return false;
	}

}
