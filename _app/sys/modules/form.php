<?php

namespace sys\modules;

use sys\Loader;
use sys\configs\Form as __form;
use sys\configs\Validation as __validation;

abstract class Form
{
	
	use Loader;

	public $form_id, $action, $method, $format, $title;

	public $form = [], $fields = [], $hidden = [], $button = [], $weight = [], $values = [], $fieldset = [];

	protected $validate = [], $sanitize = [];

	protected $status, $result, $message, $callback, $expire;

	abstract public function fields();

	abstract public function submit();

	public function request()
	{
		$this->load()->load('sys/modules/request');
		$this->load()->load('sys/modules/validation');
		
		$this->request->method = $this->method;
		$this->request->format = $this->format;
		
		foreach ($this->sanitize as $id => $filters) {
			foreach ($filters as $filter) {
				$this->request->request($id, $this->validation->sanitize($this->request->request($id), $filter));
			}
		}
		foreach ($this->validate as $id => $rules) {
			foreach ($rules as $rule) {
				$this->validation->resolve($id, $this->request->request($id), $rule['rule'], $rule['message']);
			}
		}
		if (!$this->result()) {
			return $this->status;
		}
		$this->submit($this->values = $this->request->fields($this->form_id, $this->method), $this->status);
		
		if (!$this->result()) {
			return $this->status;
		}
		if ((isset($this->expire)) ? $this->expire : $this->expire()) {
			$this->session->drop([$this->form_id => 'token']);
		}
		return $this->result(false);
	}

	public function get($form = null, $param = 'ajax')
	{	
		$this->form_id = strtolower(\sys\utils\Helper::class_name($this));
		
		if (!is_array($form)) {
			$form = ['title' => $form];
		}
		if (!isset($form['action'])) {
			$form['action'] = \sys\utils\Path::route($param . '/' . $this->form_id);
		}
		if (!isset($form['method'])) {
			$form['method'] = __form::method__;
		}
		if (!isset($form['format'])) {
			$form['format'] = __form::format__;
		}
		$this->title = $form['title'];
		$this->action = $form['action'];
		$this->method = $form['method'];
		$this->format = $form['format'];
		
		$this->secure();
		$this->fields($form);
		
		$form['id'] = $this->form_id;
		$form['fields'] = $this->fields;
		$form['hidden'] = $this->hidden;
		$form['button'] = $this->button;
		$form['fieldset'] = $this->fieldset;
		
		$form['attr'] = (isset($form['attr'])) ? (array) $form['attr'] : [];
		$form['attr']['id'] = $form['id'];
		$form['attr']['action'] = $form['action'];
		$form['attr']['method'] = $form['method'];
		
		return $this->form = $form;
	}

	public function render($template = __form::template__) // @todo use full path v form to app
	{
		return $this->load()->init('sys/view')->view('app/views/templates/form/' . $template . '/form', ['form' => $this->form]);
	}

	protected function secure()
	{
		$this->load()->load('sys/modules/session');
		
		if (!$this->session->get([$this->form_id => 'token'])) {
			$this->session->set([$this->form_id => 'token'], $this->session->hash($this->form_id, 'sha256'));
		}
		$session_token = $this->session->token();
		$request_token = $this->session->get('_request');
		
		$this->hidden('_request_' . $session_token, $request_token);
		$this->hidden('_token_' . $session_token, $this->session->get([$this->form_id => 'token']));
		
		$this->validate('_request_' . $session_token, ['header' => [$session_token => $request_token]]);
		$this->validate('_token_' . $session_token, ['session' => [$this->form_id => 'token']]);
	}

	public function result($key = 'status')
	{
		$this->status['status'] = ($this->validation->getResult(__validation::error__)) ? __validation::error__ : __validation::success__;
		$this->status['message'] = (isset($this->message[$this->status['status']])) ? $this->message[$this->status['status']] : '';
		$this->status['validation'] = $this->validation->getResult();
		$this->status['callback'] = $this->callback;
		$this->status['expire'] = $this->expire;
		$this->status['status'] = ($this->status['status'] == __validation::error__) ? false : true;
		
		return ($key) ? ((isset($this->status[$key])) ? $this->status[$key] : false) : $this->status;
	}

	public function validate($id, $rule = null, $message = null)
	{
		$id = $this->fieldId($id);
		return $this->validate[$id][] = ['rule' => $rule, 'message' => $message];
	}

	public function sanitize($id, $filter = null)
	{
		$id = $this->fieldId($id);
		return $this->sanitize[$id][] = $filter;
	}

	public function status($id = null, $status = __validation::error__)
	{
		return ($id) ? $this->validation->getStatus($this->fieldId($id), $status) : $this->validation->getResult($status);
	}

	public function error($id = null, $message = null)
	{
		return $this->validation->setStatus(($id) ? $this->fieldId($id) : __validation::error__, __validation::error__, $message);
	}

	public function success($id = null, $message = null)
	{
		return $this->validation->setStatus(($id) ? $this->fieldId($id) : __validation::success__, __validation::success__, $message);
	}

	public function message($message = null, $status = __validation::success__)
	{
		return $this->message[$status] = $message;
	}

	public function callback($name, $args = null)
	{
		return $this->callback = ['name' => $name, 'args' => (array) $args];
	}

	public function expire($expire = true)
	{
		return $this->expire = (bool) $expire;
	}

	public function fieldId($id)
	{
		return $this->form_id . '_' . $id;
	}

	public function label($label = null)
	{
		if (!is_array($label)) {
			$label = ['value' => $label];
		}
		$label['attr'] = (isset($label['attr'])) ? (array) $label['attr'] : [];
		
		return $label;
	}

	protected function field($id, $field, $label = null)
	{
		if (!isset($field['fieldset'])) {
			$field['fieldset'] = __form::fieldset__;
		}
		if (!isset($this->weight['group'][$id])) {
			$this->fieldset($field['fieldset'], $this->title, $id);
		}
		$this->weight['field'][$field['id']][] = $id;
		$this->weight['group'][$id][] = $field['id'];
		
		if ($label) {
			$this->fields[$id]['label'] = $this->label($label);
		}
		$this->fields[$id]['field'][] = $field;
		
		return $this->fields[$id];
	}

	public function fieldset($id, $title = null, $field_id = null)
	{
		$id = $this->fieldId($id);
		
		if (!isset($this->fieldset[$id])) {
			$this->fieldset[$id] = ['title' => $title, 'fields' => []];
		}
		if ($field_id) {
			if (array_search($field_id, $this->fieldset[$id]['fields']) === false) {
				$this->fieldset[$id]['fields'][] = $field_id;
			}
		}
		return $this->fieldset[$id];
	}

	public function input($id, $label = null, $input = null)
	{
		$this->sanitize($id, 'strip');
		
		$id = $this->fieldId($id);
		
		if (!is_array($input)) {
			$input = ['value' => $input];
		}
		if (!isset($input['value'])) {
			$input['value'] = '';
		}
		if (!isset($input['type'])) {
			$input['type'] = 'text';
		}
		if (!isset($input['label'])) {
			$input['label'] = '';
		}
		$input['id'] = $id;
		$input['control'] = 'input';
		$input['label'] = $this->label($input['label']);
		
		$input['attr'] = (isset($input['attr'])) ? (array) $input['attr'] : [];
		$input['attr']['value'] = $input['value'];
		$input['attr']['type'] = $input['type'];
		$input['attr']['name'] = $id;
		$input['attr']['id'] = $id;
		
		if (isset($input['group'])) {
			$id = $this->fieldId($input['group']);
		}
		$weight = (isset($this->weight['field'][$input['id']])) ? count($this->weight['field'][$input['id']]) : 0;
		
		if ($weight > 0) {
			if ($weight < 2) {
				$occur = array_search($input['id'], $this->weight['group'][$id]);
				$this->fields[$id]['field'][$occur]['attr']['name'] .= '[]';
			}
			$input['attr']['name'] .= '[]';
			$input['attr']['id'] .= '-' . $weight;
		}
		return $this->field($id, $input, $label);
	}

	public function select($id, $label = null, $select = [])
	{
		$this->sanitize($id, 'strip');
		
		$id = $this->fieldId($id);
		
		if (!isset($select['options'])) {
			$select['options'] = $select;
		}
		if (!isset($select['label'])) {
			$select['label'] = '';
		}
		$select['id'] = $id;
		$select['control'] = 'select';
		$select['label'] = $this->label($select['label']);
		
		$select['attr'] = (isset($select['attr'])) ? (array) $select['attr'] : [];
		$select['attr']['name'] = $id;
		$select['attr']['id'] = $id;
		
		if (isset($select['multiple'])) {
			$select['attr']['name'] .= '[]';
			$select['attr']['multiple'] = '';
		}
		foreach ($select['options'] as &$option) {
			$option['control'] = 'option';
			$option['attr'] = (isset($option['attr'])) ? (array) $option['attr'] : [];
			if (!isset($option['label'])) {
				$option['label'] = '';
			}
			if (!isset($option['value'])) {
				$option['value'] = '';
			}
			$option['attr']['value'] = $option['value'];
		}
		unset($option);
		
		if (isset($select['group'])) {
			$id = $this->fieldId($select['group']);
		}
		return $this->field($id, $select, $label);
	}

	public function markup($id, $label = null, $markup = null)
	{
		$id = $this->fieldId($id);
		
		if (!is_array($markup)) {
			$markup = ['value' => $markup];
		}
		if (!isset($input['value'])) {
			$input['value'] = '';
		}
		$markup['id'] = $id;
		$markup['control'] = 'markup';
		
		return $this->field($id, $markup, $label);
	}

	public function hidden($id, $hidden = null)
	{
		$this->sanitize($id, 'strip');
		
		$id = $this->fieldId($id);
		
		if (!is_array($hidden)) {
			$hidden = ['value' => $hidden];
		}
		if (!isset($hidden['value'])) {
			$hidden['value'] = '';
		}
		$hidden['id'] = $id;
		$hidden['control'] = 'hidden';
		
		$hidden['attr'] = (isset($hidden['attr'])) ? (array) $hidden['attr'] : [];
		$hidden['attr']['value'] = $hidden['value'];
		$hidden['attr']['type'] = 'hidden';
		$hidden['attr']['name'] = $id;
		$hidden['attr']['id'] = $id;
		
		return $this->hidden[$id] = $hidden;
	}

	public function button($id, $label = null, $button = [])
	{
		$id = $this->fieldId($id);
		
		if (!isset($button['type'])) {
			$button['type'] = 'submit';
		}
		$button['id'] = $id;
		$button['control'] = 'button';
		$button['label'] = $label;
		
		$button['attr'] = (isset($button['attr'])) ? (array) $button['attr'] : [];
		$button['attr']['type'] = $button['type'];
		$button['attr']['id'] = $id;
		
		return $this->button[$id] = $button;
	}

}
