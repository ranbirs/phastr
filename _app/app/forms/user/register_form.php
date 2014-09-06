<?php

namespace app\forms\user;

use sys\modules\Form;

class Register_form extends Form
{

    public function fields()
    {
        $this->load()->module('vocab', 'app');

        $this->input('register_name', 'Name',
            $params = ['attr' => ['class' => 'form-control']]);

        $this->validate('register_name', ['maxlength' => 64]);

        $this->input('register_email', 'Email',
            $params = ['attr' => ['class' => 'form-control']]);

        $this->validate('register_email', 'email');
        $this->validate('register_email', ['maxlength' => 128]);

        $this->input('register_password', 'Password',
            $params = ['type' => 'password', 'attr' => ['class' => 'form-control']]);

        $this->validate('register_password', ['maxlength' => 32]);
        $this->validate('register_password', ['minlength' => 8]);

        $this->button('register_submit', 'Submit',
            $params = ['attr' => ['class' => ['btn', 'btn-primary']]]);

        $this->message($this->vocab->t('register_success', 'user'), 'success');
        $this->message($this->vocab->t('register_fail', 'user'), 'error');
    }

    public function submit($values = null, $status = null)
    {
        if (!$this->user_model->register($values['register_name'], $values['register_email'],
            $values['register_password'])
        ) {
            $this->error();
        }
    }

}
