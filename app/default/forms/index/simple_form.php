<?php

namespace app\forms\index;/** */

class Simple_form extends \sys\modules\Form {

	function __construct()
	{
		parent::__construct();
	}

	protected function build($import = null)
	{
		$this->field(array('input' =>'text'), "this_field", "",
			$params = array(
				'validate' => array(
					'required',
					'maxlength' => 128,
					'alnum' => array('error' => "this field must be alphanumeric!"),
					'match' => array('value' => "this", 'error' => "You would have to type 'this'", 'success' => "this is correct!"),
				),
				'attr' => array('placeholder' => "Type 'this' ..."),
				'prop' => array('required'),
				'help' => "this is validated"
			)
		);


		$this->field(array('input' => 'checkbox'), "optional_checkbox", "Checkbox",
			$params = array(
					array(
						'value' => "optional_option1",
						'label' => "Optionally"
					),
					array(
						'value' => "optional_option2",
						'label' => "A box is checked",
						'prop' => array('checked'),
						'attr' => array("data-check-a-box" => $import)
					)
				)
		);

		$this->field(array('input' => 'radio'), "radio_field", "Radio",
			$params = array(
				'value' => array(
					array(
						'value' => 'on',
						'label' => "On"
					),
					array(
						'value' => 'off',
						'label' => "Off"
					)
				),
				'validate' => array('required' => array('error' => "Radio needs checking!"))
			)
		);

		$this->field(array('button' => 'submit'), "this_submit", "Submit this!", 
			$params = array(
				'css' => array("btn", "btn-primary")
			)
		);

		$this->expire(false);
		$this->success("That's about this!");
	}

	protected function resolve($submit = null, $import = null)
	{
		//if (isset($submit['this_field']) and )
		return true;
	}

}
