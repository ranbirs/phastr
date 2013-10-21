<?php

namespace sys\modules;

use sys\Init;
use sys\modules\Validation;
use sys\utils\Helper;
use sys\utils\Hash;
use sys\utils\Html;

abstract class Form {

	protected $form_id, $method, $import, $request, $validation;
	protected $field = [], $build = [], $fields = [];
	protected $validated = [], $sanitized = [];
	protected $expire, $success, $fail, $error;

	function __construct()
	{
		$this->request = Init::request();
		$this->form_id = strtolower(Helper::getClassName(get_class($this)));

		if (!Init::session()->get($this->form_id, 'token'))
			Init::session()->set([$this->form_id => 'token'], Hash::rand());
	}

	abstract protected function build();

	public function id()
	{
		return $this->form_id;
	}

	public function method()
	{
		return $this->method;
	}

	public function submit()
	{
		$method = $this->method;
		$this->validation = new Validation();

		foreach ($this->sanitized as $id => $filter)
			$this->requet->$method($id, $this->validation->sanitize($filter, $this->ajax->$method($id)));
		foreach ($this->validated as $id => $validation)
			$this->validation->resolve($id, $validation, $this->request->$method($id));

		if (array_key_exists(Validation::error__, $this->validation->get())) {
			if (!isset($this->error))
				$this->error();
			$this->error['validation'] = $this->validation->get();
			return $this->error;
		}
		if ($this->resolve($this->request->fields($this->form_id, $this->method), $this->import)) {
			if ((isset($this->expire)) ? $this->expire : $this->expire())
				Init::session()->drop($this->form_id, 'token');
			return (isset($this->success)) ? $this->success : $this->success();
		}
		return (isset($this->fail)) ? $this->fail : $this->fail();
	}

	public function html($import = null, $title = null, $attr = [], $method = 'post', $template = "bootstrap")
	{
		$this->method = $method;
		$this->import = $import;
		$this->build($import);
		$this->close();

		$attr['id'] = $this->form_id;
		$attr['method'] = $method;
		$attr['action'] = Helper::getPath(['form', $this->form_id], Request::param__) . "/";
		$this->build['title'] = $title;
		$this->build['attr'] = $attr;

		$form = ['build' => $this->build, 'fields' => $this->fields];
		return Init::view()->template('form', $template, $form);
	}

	protected function close()
	{
		$header_id = Init::session()->token();
		$session_key = Init::session()->key();

		$this->field(['input' => 'hidden'], "_header_id_" . $session_key, null,
			$params = [
				'value' => $header_id,
				'validate' => ['header' => [$session_key => $header_id]]
			]
		);
		$this->field(['input' => 'hidden'], "_form_id_" . $session_key, null,
			$params = [
				'value' => $this->form_id,
				'validate' => [$this->method => [$this->form_id . "__form_id_" . $session_key => $this->form_id]]
			]
		);
		$this->field(['input' => 'hidden'], "_form_token_" . $session_key, null,
			$params = [
				'value' => Init::session()->get($this->form_id, 'token'),
				'validate' => ['token' => $this->form_id]
			]
		);
	}

	protected function resolve($submit = null, $import = null)
	{
		return true;
	}

	protected function error($msg = "", $callback = "")
	{
		return $this->error = ['result' => false, 'callback' => $callback, 'message' => $msg];
	}

	protected function fail($msg = "", $callback = "")
	{
		return $this->fail = ['result' => false, 'callback' => $callback, 'message' => $msg];
	}

	protected function success($msg = "", $callback = "")
	{
		return $this->success = ['result' => true, 'callback' => $callback, 'message' => $msg];
	}

	protected function expire($expire = true)
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
			if ($control == 'input')
				$type = 'text';
			if ($control == 'button')
				$type = 'button';
		}
		if (!is_array($params))
			$params = ['value' => $params];
		if (!isset($params['value']))
			$params['value'] = (array_values($params) !== $params) ? "" : $params;
		$params['label'] = $label;
		$params['control'] = "";

		$this->field = $params;

		return $this->_buildField($this->form_id . "_" . $id, $control, $type);
	}

	private function _buildField($id, $control, $type = null)
	{
		if ($control == 'markup') {
			$this->field['control'] = $this->field['value'];
			return $this->fields[$id]['field'] = $this->field;
		}
		$build = "";
		$group = null;

		$this->field['attr']['id'] = $id;
		$this->field['attr']['name'] = (!is_array($this->field['value'])) ? $id : $id . "[]";
		if (!is_null($type))
			$this->field['attr']['type'] = $type;

		if (isset($this->field['attr']['class']) and !is_array($this->field['attr']['class']))
			$this->field['attr']['class'] = [$this->field['attr']['class']];

		if ($control != 'button') {
			$this->field['attr']['class'][] = "field";
			$this->field['attr']['class'][] = "form-control";
		}
		if (isset($this->field['sanitize']))
			$this->sanitized[$id] = $this->field['sanitize'];

		if (isset($this->field['validate'])) {
			$this->validated[$id] = $this->field['validate'];
			foreach ($this->field['validate'] as $rule => $params)
				if (is_array($params) and array_intersect([Validation::error__, Validation::success__], array_keys($params)))
					$this->field['verbose'] = true;
		}
		if (is_array($this->field['value']))
			$this->field['control'] = $this->_buildArrayField($id, $control);

		switch ($control) {
			case 'input':
				switch ($type) {
					case 'hidden':
						$group = $type;
						break;
				}
				(!is_array($this->field['control'])) ?
					$this->field['attr']['value'] = $this->field['value'] :
					$group = $id;
				break;
			case 'select':
				$options = [];
				foreach ($this->field['control'] as $field)
					$options[] = $field['field']['control'];
				$build = eol__ . implode(eol__, $options) . eol__ . "</" . $control . ">";
				break;
			case 'button':
				$build = $this->field['label'] . "</" . $control . ">";
				$this->field['label'] = "";
				$group = 'action';
				break;
			case 'textarea':
				$build = "</" . $control . ">";
				break;
		}
		$build = "<" . $control . Html::getAttr($this->field['attr']) . ">" . $build;

		if (!is_null($group))
			$id = $group;

		switch ($group) {
			case 'hidden':
			case 'action':
				$this->field['control'] = $build;
				$this->fields[$id]['field']['control'][] = $this->field['control'];
				break;
			case null:
				$this->field['control'] = $build;
			default:
				$this->fields[$id]['field'] = $this->field;
		}
		return $this->fields[$id]['field'];
	}

	private function _buildArrayField($id, $control)
	{
		$fields = [];

		foreach ($this->field['value'] as $index => &$field) {

			if (!is_array($field))
				$field = [$field];

			if (!isset($field['value'])) {
				if (array_values($field) !== $field) {
					$field['value'] = key($field);
					$field['label'] = current($field);
				}
				else {
					$field['value'] = current($field);
					$field['label'] = "";
				}
			}
			if (!isset($field['label']))
				$field['label'] = "";

			$field['attr']['value'] = $field['value'];

			$field['control'] = $control;
			$build = "";

			switch ($control) {
				case 'select':
					$field['control'] = 'option';
					$field['active'] = (isset($field['attr']['selected'])) ? true : false;
					$build = $field['label'] . "</" . $field['control'] . ">";
					break;
				default:
					$field['attr']['id'] = $id . "-" . $index;
					$field['attr']['name'] = $id . "[]";
					$field['attr'] = Helper::getAttr(array_merge($this->field['attr'], $field['attr']));
					$field['active'] = (isset($field['attr']['checked'])) ? true : false;
			}
			$build = "<" . $field['control'] . Html::getAttr($field['attr']) . ">" . $build;
			$field['control'] = $build;

			$field = ['field' => $field];
			$fields[] = $field;
		}
		unset($field);
		return $fields;
	}

}
