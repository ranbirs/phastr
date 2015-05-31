<?php

namespace app\forms\index;

use sys\modules\Form;

class Simple_form extends Form
{

	public function fields()
	{
		$this->input('test_custom_validation_field', ' ', 
			$params = ['attr' => ['placeholder' => 'A number that starts with 123...', 'class' => 'form-control']]);
		
		$this->validate('test_custom_validation_field', 'require');
		$this->validate('test_custom_validation_field', 'int', 'That\'s not a number!');
		
		$this->input('test_number_field_optional', ' ', 
			$params = ['type' => 'number', 'attr' => ['placeholder' => 'Optional number field...', 'class' => 'form-control']]);
		
		$this->button('submit_button', 'Submit', $params = ['attr' => ['class' => ['btn', 'btn-primary'], 'data-loading-text' => 'Loading...']]);
		
		$this->expire(false);
		$this->message(
			'<strong>Good job...</strong><br>The token for this form will not "expire" during the current session (so it may be re-submitted successfully without refreshing the page).', 
			'success');
	}

	public function submit($values = null, $status = null)
	{
		if (strpos($values['test_custom_validation_field'], '123') !== 0) {
			$this->error('test_custom_validation_field', 'That number doesn\'t start with "123".');
			$this->message('Wrong number!', 'error');
		} else {
			$this->success('test_custom_validation_field', 'Nice. ' . $values['test_custom_validation_field'] . ' starts with "123".');
		}
	}

}
