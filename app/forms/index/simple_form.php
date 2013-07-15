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
			$build = array(
				'attr' => array('placeholder' => "Type a number..."),
				'validate' => array(
					'maxlength' => array('value' => 128),
					'required'
				)
			)
		);

		$this->field(array('input' => 'text'), "test_text_field_required", "",
			$build = array(
				'attr' => array('placeholder' => "Optional text..."),
				'validate' => array(
					'maxlength' => array('value' => 32)
				)
			)
		);

		$this->field(array('button' => 'submit'), "submit_button", "Submit", 
			$build = array(
				'css' => array("btn", "btn-primary"),
				'attr' => array('data-loading-text' => "Loading...")
			)
		);

		$this->expire(false);
		$this->success('<p>Good job...</p>' .
			'<p>The token for this form will not "expire" during the current session (so it may be re-submitted successfully without refreshing the page).</p>'
		);
	}

}
