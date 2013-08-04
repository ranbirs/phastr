<?php

namespace app\forms\index;

class Simple_form extends \sys\modules\Form {

	function __construct()
	{
		parent::__construct();
	}

	protected function build()
	{
		$this->field(array('input' =>'number'), "test_email_field", "",
			$params = array(
				'attr' => array('placeholder' => "Type a number..."),
				'validate' => array(
					'maxlength' => 128,
					'required'
				)
			)
		);

		$this->field(array('input' => 'text'), "test_text_field_required", "",
			$params = array(
				'attr' => array('placeholder' => "Optional text..."),
				'validate' => array(
					'maxlength' => 32
				)
			)
		);

		$this->field(array('button' => 'submit'), "submit_button", "Submit", 
			$params = array(
				'css' => array("btn", "btn-primary"),
				'attr' => array('data-loading-text' => "Loading...")
			)
		);

		$this->expire(false);
		$this->success('<strong>Good job...</strong><br>' .
			'The token for this form will not "expire" during the current session (so it may be re-submitted successfully without refreshing the page).'
		);
	}

}
