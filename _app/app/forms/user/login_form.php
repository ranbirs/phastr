<?php

namespace app\forms\user;

use sys\modules\Form;

class Login_form extends Form
{

	public function fields()
	{
		$this->input('login_email', 'Email', $params = ['attr' => ['class' => 'form-control']]);
		
		$this->validate('login_email', 'email');
		$this->validate('login_email', ['maxlength' => 128]);
		
		$this->input('login_password', 'Password', $params = ['type' => 'password', 'attr' => ['class' => 'form-control']]);
		
		$this->validate('login_password', ['maxlength' => 32], 'max');
		$this->validate('login_password', ['minlength' => 8], 'min');
		
		$this->button('login_submit', 'Sign in', $params = ['attr' => ['class' => ['btn', 'btn-primary']]]);
	}

	public function submit($values = null, $status = null)
	{
		$this->message('You have successfully signed in', 'success');
		$this->message('Invalid credentials', 'error');
		
		if (!$this->user_model->login($values['login_email'], $values['login_password'])) {
			$this->error();
		}
	}

}
