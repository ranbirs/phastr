<?php

namespace sys\modules;

use sys\modules\Validation;

abstract class Form
{
	use \sys\Loader;

	protected $form_id, $method, $import, $submit;

	protected $build = [], $fields = [], $hidden = [], $action = [];

	protected $validated = [], $sanitized = [];

	protected $status, $expire, $message, $callback;

	function __construct()
	{
		$this->load()->init('view');
		$this->load()->module('session');
		$this->load()->module('hash');
		$this->load()->util('helper');
		$this->load()->util('path');
		$this->load()->util('html');
	}

	abstract public function build();

	abstract public function submit();

	public function id()
	{
		return $this->form_id;
	}

	public function method()
	{
		return $this->method;
	}

	public function resolve($layout = 'json')
	{
		$this->load()->module('request');
		$this->load()->module('validation');
		
		$this->request->layout = $layout;
		
		foreach ($this->sanitized as $id => $filter) {
			$this->request->{$this->method}($id, 
				$this->validation->sanitize($this->request->{$this->method}($id), $filter));
		}
		foreach ($this->validated as $id => $validation) {
			$this->validation->resolve($id, $validation, $this->request->{$this->method}($id));
		}
		$this->submit = $this->request->fields($this->form_id, $this->method);
		
		if (!$this->status()) {
			return $this->status;
		}
		$this->validate($this->submit, $this->status);
		
		if (!$this->status()) {
			return $this->status;
		}
		$this->submit($this->submit, $this->status);
		
		if ((isset($this->expire)) ? $this->expire : $this->expire()) {
			$this->session->drop($this->form_id, 'token');
		}
		if ($this->status()) {
			return $this->status;
		}
		
		return false;
	}

	public function render($import = null, $title = null, $attr = [], $method = 'post', $template = 'bootstrap')
	{
		$this->form_id = strtolower($this->helper->className($this));
		$this->import = $import;
		$this->method = $method;
		$this->build($import);
		$this->close();
		
		$attr['id'] = $this->form_id;
		$attr['method'] = $method;
		$attr['action'] = $this->path->request('form/' . $this->form_id);
		$this->build['title'] = $title;
		$this->build['attr'] = $attr;
		
		$form = ['build' => $this->build, 'fields' => $this->fields, 'action' => $this->action, 
			'hidden' => $this->hidden];
		return $this->view->template('form', $template, $form);
	}

	public function close()
	{
		if (!$this->session->get($this->form_id, 'token')) {
			$this->session->set([$this->form_id => 'token'], $this->hash->rand('sha256'));
		}
		$session_token = $this->session->token();
		$session_key = $this->session->key();
		
		$this->field(['input' => 'hidden'], '_header_id_' . $session_token, null, 
			$params = ['value' => $session_key, 'validate' => [
				'header' => [$session_token => $session_key]]]);
		$this->field(['input' => 'hidden'], '_form_id_' . $session_token, null, 
			$params = ['value' => $this->form_id, 
				'validate' => [$this->method => [
					$this->form_id . '__form_id_' . $session_token => $this->form_id]]]);
		$this->field(['input' => 'hidden'], '_form_token_' . $session_token, null, 
			$params = ['value' => $this->session->get($this->form_id, 'token'), 
				'validate' => ['token' => $this->form_id]]);
	}

	public function status()
	{
		$this->status['status'] = (array_key_exists(Validation::error__, 
			$this->status['validation'] = $this->validation->getResult())) ? 0 : 1;
		$this->status['message'] = (isset($this->message[$this->status['status']])) ? $this->message[$this->status['status']] : '';
		$this->status['callback'] = (string) $this->callback;
		
		return $this->status['status'];
	}

	public function validate($submit = null, $status = null)
	{
		return true;
	}

	public function getValidation()
	{
		return $this->validation->getResult();
	}

	public function setValidation($id, $message = null, $status = false)
	{
		$status = (!$status) ? Validation::error__ : Validation::success__;
		return $this->validation->setStatus($this->form_id . '_' . $id, $message, $status);
	}

	public function message($message, $status = true)
	{
		$status = (int) $status;
		return $this->message[$status] = $message;
	}

	public function callback($callback = '')
	{
		return $this->callback = $callback;
	}

	public function expire($expire = true)
	{
		return $this->expire = (bool) $expire;
	}

	protected function field($control, $id, $label = null, $params = null)
	{
		$type = null;
		if (is_array($control)) {
			$type = current($control);
			$control = key($control);
		}
		if (is_null($type)) {
			if ($control == 'input') {
				$type = 'text';
			}
			if ($control == 'button') {
				$type = 'button';
			}
		}
		if (!is_array($params)) {
			$params = ['value' => $params];
		}
		if (!isset($params['value'])) {
			$params['value'] = ($params !== array_values($params)) ? '' : $params;
		}
		$params['attr'] = (isset($params['attr'])) ? (array) $params['attr'] : [];
		
		$params['label'] = $label;
		$params['control'] = '';
		
		return $this->_parseField($this->form_id . '_' . $id, $control, $type, $params);
	}

	private function _parseField($id, $control, $type = null, $field = [])
	{
		if ($control == 'markup') {
			$field['control'] = $field['value'];
			return $this->fields[$id] = $field;
		}
		$build = '';
		$group = null;
		
		$field['attr']['id'] = $id;
		if (!is_null($type)) {
			$field['attr']['type'] = $type;
		}
		if (isset($field['attr']['class'])) {
			$field['attr']['class'] = (array) $field['attr']['class'];
		}
		if ($control != 'button') {
			$field['attr']['name'] = (!is_array($field['value'])) ? $id : $id . '[]';
			$field['attr']['class'][] = 'form-control';
			if (!isset($field['sanitize'])) {
				$field['sanitize'] = ['strip' => FILTER_FLAG_ENCODE_LOW];
			}
		}
		if (isset($field['sanitize'])) {
			$this->sanitized[$id] = $field['sanitize'];
		}
		if (isset($field['validate'])) {
			$this->validated[$id] = $field['validate'];
			foreach ($field['validate'] as $rule => $params) {
				if (is_array($params) && array_intersect([Validation::error__, Validation::success__], 
					array_keys($params))) {
					$field['feedback'] = true;
				}
			}
		}
		if (is_array($field['value'])) {
			$this->_parseArrayField($id, $control, $field['value'], $field);
		}
		switch ($control) {
			case 'input':
				if ($type == 'hidden') {
					$group = $type;
				}
				(!is_array($field['control'])) ? $field['attr']['value'] = $field['value'] : $group = $id;
				break;
			case 'select':
				$options = [];
				foreach ($field['control'] as $option) {
					$options[] = $option['control'];
				}
				$build = eol__ . implode(eol__, $options) . eol__ . '</' . $control . '>';
				break;
			case 'button':
				$build = $field['label'] . '</' . $control . '>';
				$field['label'] = '';
				$group = 'action';
				break;
			case 'textarea':
				$build = '</' . $control . '>';
				break;
		}
		$build = '<' . $control . $this->html->attr($field['attr']) . '>' . $build;
		
		switch ($group) {
			case 'hidden':
			case 'action':
				$field['control'] = $build;
				$this->{$group}[] = $field;
				break;
			case null:
				$field['control'] = $build;
			default:
				$this->fields[$id] = $field;
		}
		// return $this->fields[$id];
	}

	private function _parseArrayField($id, $control, &$values, &$parent)
	{
		$fields = $values;
		$values = [];
		
		foreach ($fields as $index => &$field) {
			$field = (array) $field;
			if (!isset($field['value'])) {
				if (array_values($field) !== $field) {
					$field['value'] = key($field);
					$field['label'] = current($field);
				}
				else {
					$field['value'] = current($field);
					$field['label'] = '';
				}
			}
			$values[] = $field['value'];
			
			if (!isset($field['label'])) {
				$field['label'] = '';
			}
			
			$field['attr'] = (isset($field['attr'])) ? (array) $field['attr'] : [];
			$field['attr']['value'] = $field['value'];
			$field['control'] = $control;
			
			$build = '';
			
			switch ($control) {
				case 'select':
					$field['control'] = 'option';
					$field['active'] = (isset($field['attr']['selected'])) ? true : false;
					$build = $field['label'] . '</' . $field['control'] . '>';
					break;
				default:
					$field['attr']['id'] = $id . '-' . $index;
					$field['attr']['name'] = $id . '[]';
					$field['attr'] = $this->helper->attr(array_merge($parent['attr'], $field['attr']));
					$field['active'] = (isset($field['attr']['checked'])) ? true : false;
			}
			$build = '<' . $field['control'] . $this->html->attr($field['attr']) . '>' . $build;
			$field['control'] = $build;
		}
		unset($field);
		$parent['control'] = $fields;
	}

}
