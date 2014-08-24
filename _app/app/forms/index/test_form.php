<?php

namespace app\forms\index;

class Test_form extends \sys\modules\Form
{

	public function fields()
	{
		$required_field_msg = 'This is a required field';
		
		$this->markup('fieldset_desc', ' ',
			'<p>Fields without a fieldset default to an inclusive fieldset with the form title as the legend.</p>' .
			'<p>A label with a single space also creates the [optional] label column.</p>');
		
		$this->markup('header_markup','<em>Example data</em>',
			'<pre>' . print_r($this->imported_data, true) . '</pre>');
		
		$this->input('test_email_field', 'Email field',
			$params = [
				'type' => 'text',
				'attr' => ['class' => 'form-control'],
				'append' => '<span class="help-block">Help block/description...this field is validated</span>'
			]);

		$this->validate('test_email_field', 'require', $required_field_msg);
		$this->validate('test_email_field', 'email', ['error' => 'That doesn\'t look like an email address', 'success' => 'Looking good']);
		$this->validate('test_email_field', ['maxlength' => 128]);

		$this->input('test_text_field_require', 'Text field',
			$params = [
				'value' => 'prefilled text...',
				'attr' => ['class' => 'form-control', 'placeholder' => 'placeholder text...']
			]);

		$this->validate('test_text_field_require', 'require', $required_field_msg);
		$this->validate('test_text_field_require', ['maxlength' => 32], 'Over the limit!');
		
		$this->input('checkbox_field', 'Checkboxes',
			$params = [
				'type' => 'checkbox',
				'value' => 'afeature',
				'label' => [
					'value' => 'A feature',
					'attr' => ['class' => 'checkbox-inline']
				]
			]);
		$this->input('checkbox_field', '',
			$params = ['type' => 'checkbox', 'value' => 'another', 'label' => 'Another']);
		$this->input('checkbox_field', '',
			$params = ['type' => 'checkbox', 'value' => 'onemore', 'label' => 'One more']);
		
		$this->validate('checkbox_field', 'require', $required_field_msg);
		
		$this->input('radio_field', '',
			$params = ['type' => 'radio', 'value' => 'fm', 'label' => 'FM']);
		$this->input('radio_field', '',
			$params = ['type' => 'radio', 'value' => 'am', 'label' => 'AM']);
		$this->input('radio_field', 'Radios',
			$params = ['type' => 'radio', 'value' => 'dab', 'label' => 'DAB']);
		
		$this->validate('radio_field', 'require', $required_field_msg);

		$this->select('select_field', 'Select',
			$params = [
				'options' => [
					['value' => '', 'label' => 'select an option', 'attr' => ['data-attr' => ['data-val']]],
					['value' => 'select_option1', 'label' => 'first option'],
					['value' => 'select_option2', 'label' => 'another option'],
					['value' => 'select_option3', 'label' => '3rd option']
				],
				'attr' => ['class' => 'form-control']
			]);
		
		$this->validate('select_field', 'require', $required_field_msg);
		
		$this->select('multiple_select_field', 'Le multiple select',
			$params = [
				'options' => [
					['label' => 'select some option (tis null)'],
					['value' => 'select_option1', 'label' => 'this and...'],
					['value' => 'select_option2', 'label' => 'that', 'attr' => ['selected']],
					['value' => 'select_option3', 'label' => 'or...', 'attr' => 'selected']
				],
				'multiple' => true,
				'attr' => ['class' => 'form-control']
			]);
		
		$this->validate('multiple_select_field', 'require', $required_field_msg);

		$this->fieldset('address_fielset', 'Optionally defining a fieldset with a title...');

		$this->input('address_field', 'Practically a single field',
			$params = ['fieldset' => 'address_fielset', 'label' => 'optional labels...', 'attr' => ['class' => 'form-control']]);
		$this->input('address_field', '',
			$params = ['label' => 'address line 2', 'attr' => ['class' => 'form-control']]);
		$this->input('address_field', '',
			$params = ['label' => 'address line 3', 'attr' => ['class' => 'form-control']]);
		
		$this->validate('address_field', 'require', $required_field_msg);
		
		$this->select('test_new_select', '',
			$params = [
				'label' => 'Group in any other field...',
				'attr' => ['class' => 'form-control'],
				'group' => 'address_field',
				'options' => [
					['label' => 'Interwebz', 'value' => 'interwebz'],
					['label' => 'Internetz', 'value' => 'internetz']
				],
				'attr' => ['class' => 'form-control'],
				'append' => '<button type="button">Or append anything else</button>'
			]);

		$this->markup('footer_markup', '',
			'<hr><p>Exemplifying a "fluid" custom markup field...\'cause you won\'t get Forms like this anywhere else!</p><hr>');
		
		$this->button('submit_button', 'Submit',
			$params = ['attr' => ['class' => ['btn', 'btn-primary'], 'data-loading-text' => 'Loading...']]);
		
		$this->button('cancel_button', 'Make everything OK, faster!',
			$params = [
				'type' => 'button',
				'attr' => [
					'class' => 'btn btn-default', 
					'data-loading-text' => 'Everything is OK!',
					'onclick' => '$(this).stop().button(\'loading\');
						$(\'.controls\').popover(\'destroy\');
						$(this).animate({delay: 1}, 2000, function () {
							$(this).button(\'reset\').text(\'Is everything OK?\');
						});'
				]
			]);

		$this->message('<strong>Congratulations!</strong><br>That wasn\'t so easy...', 'success');
		$this->message('There are errors that need fixing!', 'error');
	}

	public function submit($values = null, $status = null)
	{
		return null;
	}

}
