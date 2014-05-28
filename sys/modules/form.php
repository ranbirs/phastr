<?php

namespace sys\modules;

use sys\modules\Validation as __validation;

abstract class Form
{
	
	use \sys\Loader;
	
	const method__ = 'post';
	
	const format__ = 'json';
	
	protected $method = self::method__;
	
	protected $format = self::format__;

	protected $form_id, $action;

	protected $form = [], $fields = [], $hidden = [], $button = [], $weight = [], $values = [];

	protected $validate = [], $sanitize = [];

	protected $status, $result, $message, $callback, $expire;

	function __construct()
	{
		$this->load()->module('session');
		$this->load()->module('hash');
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
	
	public function format($format = null)
	{
		return $this->format = (!$format) ? $this->format : $layout;
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
		$form['id'] = $this->form_id;

		$form['attr'] = (isset($form['attr'])) ? (array) $form['attr'] : [];

		$this->action = (!isset($form['attr']['action'])) ? \sys\utils\path\request('form/' . $this->form_id) : $form['attr']['action'];
		$this->method = (!isset($form['attr']['method'])) ? $this->method : $form['attr']['method'];
		
		$this->fields();
		$this->secure();
		
		$form['attr']['id'] = $this->form_id;
		$form['attr']['action'] = $this->action;
		$form['attr']['method'] = $this->method;
		
		$form['fields'] = $this->fields;
		$form['hidden'] = $this->hidden;
		$form['button'] = $this->button;
		
		return $this->form = $form;
	}

	public function render($template = 'bootstrap/form')
	{
		return $this->load()->init('view')->template('form', $template, $this->form);
	}
	
	protected function secure()
	{
		if (!$this->session->get([$this->form_id => 'token'])) {
			$this->session->set([$this->form_id => 'token'], $this->hash->rand('sha256'));
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
		$this->status['status'] = $this->validation->getResult(__validation::error__) ? __validation::error__ : __validation::success__;
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
	
	public function status($id = null, $status = __validation::error__)
	{
		return ($id) ? $this->validation->getStatus($this->field_id($id), $status) : $this->validation->getResult($status); 
	}
	
	public function error($id = null, $message = '')
	{
		return $this->validation->setStatus(($id) ? $this->field_id($id) : __validation::error__, __validation::error__, $message);
	}
	
	public function success($id = null, $message = '')
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
		$this->weight['field'][$input['id']][] = $id;
		$this->weight['group'][$id][] = $input['id'];
	
		if ($label) {
			$this->fields[$id]['label'] = $this->label($label);
		}
		$this->fields[$id]['field'][] = $input;
	
		return $this->fields[$id];
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
		$this->weight['field'][$select['id']][] = $id;
		$this->weight['group'][$id][] = $select['id'];

		if ($label) {
			$this->fields[$id]['label'] = $this->label($label);
		}
		$this->fields[$id]['field'][] = $select;
		
		return $this->fields[$id];
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
		
		if ($label) {
			$this->fields[$id]['label'] = $this->label($label);
		}
		$this->fields[$id]['field'][] = $markup;
		
		return $this->fields[$id];
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
