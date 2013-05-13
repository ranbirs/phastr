<?php

namespace app\forms\index;

class Test_form extends \sys\modules\Form {

	function __construct()
	{
		parent::__construct();
	}

	protected function build($import)
	{
		$this->open("Example Form", array("form-horizontal"));

		$this->field('markup', "", "<em>Example data</em>",
			$data = array('value' => "<pre>" . print_r($import, true) . "</pre>")
		);

		$this->field(array('input' =>'email'), "test_email_field", "Email field",
			$data = array(
				'helptext' => "Field is validated",
				'validate' => array(
					'email' => array(
						'error' => "Invalid email address",
						'success' => "Looking good"
					),
					'maxlength' => array('value' => 128),
					'required' => array('error' => "Required field")
				)
			)
		);

		$this->field(array('input' => 'text'), "test_text_field_required", "Text field",
			$data = array(
				'attr' => array('placeholder' => "placeholder text..."),
				'validate' => array(
					'maxlength' => array('value' => 32),
					'required' => array('error' => "Required field")
				)
			)
		);

		$this->field(array('input' => 'checkbox'), "checkbox_field", "Checkboxes",
			$data = array(
				'value' => array(
					array(
						'value' => "somefeature",
						'label' => "some feature"
					),
					array(
						'value' => "anotheroption",
						'label' => "another option",
						'css' => array("somecssclass", "someothercssclass")
					),
					array(
						'value' => "other",
						'label' => "something else",
						'attr' => array('data-someattr' => "someattr data"),
						'prop' => array('checked')
					)
				),
				'validate' => array('required' => array('error' => "Required field"))
			)
		);

		$this->field(array('input' => 'radio'), "radio_field", "Radio buttons",
			$data = array(
				'value' => array(
					array(
						'value' => "fm",
						'label' => "FM"
					),
					array(
						'value' => "am",
						'label' => "AM"
					),
					array(
						'value' => "dab",
						'label' => "DAB"
					)
				),
				'validate' => array('required' => array('error' => "Required field"))
			)
		);

		$this->field('select', "select_field", "Select",
			$data = array(
				'value' => array(
					array(
						'value' => "",
						'label' => "select an option",
						'css' => array("cssclass1", "cssclass2")
					),
					array(
						'value' => "select_option1",
						'label' => "first option",
						'attr' => array('data-someotherattr' => "some other data")
					),
					array(
						'value' => "select_option2",
						'label' => "some other option"
					),
					array(
						'value' => "select_option3",
						'label' => "3rd option"
					)
				),
				'validate' => array('required' => array('error' => "Required field"))
			)
		);

		$this->field('select', "multiple_select_field", "Le multiple select",
			$data = array(
				'value' => array(
					array(
						'value' => "",
						'label' => "select some option (tis null)"
					),
					array(
						'value' => "select_option1",
						'label' => "this and..."
					),
					array(
						'value' => "select_option2",
						'label' => "that",
						'prop' => array('selected')
					),
					array(
						'value' => "select_option3",
						'label' => "or...",
						'prop' => array('selected')
					)
				),
				'prop' => array('multiple'),
				'validate' => array('required' => array('error' => "Required field"))
			)
		);

		$this->field(array('input' => 'text'), "address_field", "Practically a single field",
			$data = array(
				'value' => array(
					array(
						'value' => "",
						'label' => "address line 1"
					),
					array(
						'value' => "",
						'label' => "address line 2"
					),
					array(
						'value' => "",
						'label' => "address line 3"
					)
				),
				'validate' => array('required' => array('error' => "Required field"))
			)
		);

		$this->field('markup', "some_markup", null,
			$data = array('value' => "<p>More custom markup...</p>")
		);

		$this->field(array('input' => 'text'), "test_text_field", "Text field 2");

		$this->field(array('button' => 'submit'), "submit_button", "Submit", 
			$data = array(
				'css' => array("btn", "btn-primary"),
				'attr' => array('data-loading-text' => "Loading...")
			)
		);

		$this->field(array('button' => 'action'), "cancel_button", "Cancel",
			$data = array('css' => array("btn"))
		);

		$this->close();
	}

	protected function success()
	{
		return array('message' => "success message...");
	}

}
