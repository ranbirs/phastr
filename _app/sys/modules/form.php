<?php

namespace sys\modules;

use app\confs\Config as __config;
use sys\modules\Validation as __validation;

abstract class Form
{
	
	use \sys\Loader;
	
	const method__ = 'post';
	
	const format__ = 'json';
	
	const fieldset__ = 'default';

	protected $form_id, $action, $method, $format, $title;

	protected $form = [], $fields = [], $hidden = [], $button = [], $weight = [], $values = [], $fieldset = [];

	protected $validate = [], $sanitize = [];

	protected $status, $result, $message, $callback, $expire;

	function __construct()
	{
		$this->load()->module('session');
	}

	abstract public function fields();

	abstract public function submit();

	public function form_id()
	{
		return $this->form_id;
	}

	public function action()
	{
		return $this->action;
	}
	
	public function method()
	{
		return $this->method;
	}
	
	public function format()
	{
		return $this->format;
	}

	public function resolve()
	{
		$this->load()->module('request');
		$this->load()->module('validation');
		
		foreach ($this->sanitize as $id => $filters) {
			foreach ($filters as $filter) {
				$this->request->{$this->method}($id, $this->validation->sanitize($this->request->{$this->method}($id), $filter));
			}
		}
		foreach ($this->validate as $id => $rules) {
			foreach ($rules as $rule) {
				$this->validation->resolve($id, $this->request->{$this->method}($id), $rule['rule'], $rule['message']);
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

	public function get($form = null)
	{
		$this->form_id = strtolower(\sys\utils\helper\class_name($this));

		if (!is_array($form)) {
			$form = ['title' => $form];
		}
		if (!isset($form['action'])) {
			$form['action'] = \sys\utils\path\request('form/' . $this->form_id);
		}
		if (!isset($form['method'])) {
			$form['method'] = self::method__;
		}
		if (!isset($form['format'])) {
			$form['format'] = self::format__;
		}

		$this->title = $form['title'];
		$this->action = $form['action'];
		$this->method = $form['method'];
		$this->format = $form['format'];
		
		$this->fields($form);
		$this->secure();
		
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

	public function render($template = __config::form__)
	{
		return $this->load()->init('view')->template('form', $template . '/form', $this->form);
	}
	
	protected function secure()
	{
		if (!$this->session->get([$this->form_id => 'token'])) {
			$this->session->set([$this->form_id => 'token'], $this->session->hash($this->form_id, 'sha256'));
		}
		$session_token = $this->session->token();
		$session_key = $this->session->key();
	
		$this->hidden('_header_' . $session_token, $session_key);
		$this->hidden('_token_' . $session_token, $this->session->get([$this->form_id => 'token']));
	
		$this->validate('_header_' . $session_token, ['header' => [$session_token => $session_key]]);
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
		$id = $this->field_id($id);
		return $this->validate[$id][] = ['rule' => $rule, 'message' => $message];
	}

	public function sanitize($id, $filter = null)
	{
		$id = $this->field_id($id);
		return $this->sanitize[$id][] = $filter;
	}
	
	public function status($id = '', $status = __validation::error__)
	{
		return ($id) ? $this->validation->getStatus($this->field_id($id), $status) : $this->validation->getResult($status); 
	}
	
	public function error($id = '', $message = '')
	{
		return $this->validation->setStatus(($id) ? $this->field_id($id) : __validation::error__, __validation::error__, $message);
	}
	
	public function success($id = '', $message = '')
	{
		return $this->validation->setStatus(($id) ? $this->field_id($id) : __validation::success__, __validation::success__, $message);
	}

	public function message($message = '', $status = __validation::success__)
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
	
	public function field_id($id)
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
			$field['fieldset'] = self::fieldset__;
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
	
	public function fieldset($id, $title = '', $field_id = '')
	{
		$id = $this->field_id($id);

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

		$id = $this->field_id($id);

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
			$id = $this->field_id($input['group']);
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

		$id = $this->field_id($id);

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
			$select['attr']['multiple'] = 'multiple';
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
			$id = $this->field_id($select['group']);
		}
		return $this->field($id, $select, $label);
	}
	
	public function markup($id, $label = null, $markup = null)
	{
		$id = $this->field_id($id);
		
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

		$id = $this->field_id($id);
		
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
	
	public function button($id, $label = '', $button = [])
	{
		$id = $this->field_id($id);
	
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
