<?php

namespace sys\modules;

use sys\Init;
use sys\modules\Validation;
use sys\utils\Helper;
use sys\utils\Hash;
use sys\utils\Html;

abstract class Form {

	use \sys\traits\View;
	use \sys\traits\Request;

	protected $form_id, $method, $import, $validation;
	protected $build = [], $fields = [];
	protected $validated = [], $sanitized = [];
	protected $expire, $success, $fail, $error;

	function __construct()
	{

	}

	abstract protected function build();

	public function id()
	{
		if (!isset($this->form_id))
			$this->form_id = strtolower(Helper::getInstanceClassName($this));
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
			$this->request()->$method($id, $this->validation->sanitize($this->request()->$method($id), $filter));
		foreach ($this->validated as $id => $validation)
			$this->validation->resolve($id, $validation, $this->request()->$method($id));

		if (array_key_exists(Validation::error__, $this->validation->getResult())) {
			if (!isset($this->error))
				$this->error();
			$this->error['validation'] = $this->validation->getResult();
			return $this->error;
		}
		if ($this->resolve($this->request()->fields($this->id(), $this->method), $this->import)) {
			if ((isset($this->expire)) ? $this->expire : $this->expire())
				Init::session()->drop($this->id(), 'token');
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

		$attr['id'] = $this->id();
		$attr['method'] = $method;
		$attr['action'] = Helper::getPath(['form', $this->id()], Request::param__) . "/";
		$this->build['title'] = $title;
		$this->build['attr'] = $attr;

		$form = ['build' => $this->build, 'fields' => $this->fields];
		return $this->view()->template('form', $template, $form);
	}

	protected function close()
	{
		if (!Init::session()->get($this->id(), 'token'))
			Init::session()->set([$this->id() => 'token'], Hash::rand());

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
				'value' => $this->id(),
				'validate' => [$this->method => [$this->id() . "__form_id_" . $session_key => $this->id()]]
			]
		);
		$this->field(['input' => 'hidden'], "_form_token_" . $session_key, null,
			$params = [
				'value' => Init::session()->get($this->id(), 'token'),
				'validate' => ['token' => $this->id()]
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
			$params['value'] = ($params !== array_values($params)) ? "" : $params;
		if (!isset($params['attr']))
			$params['attr'] = [];
		if (!is_array($params['attr']))
			$params['attr'] = [$params['attr']];

		$params['label'] = $label;
		$params['control'] = "";

		return $this->_parseField($this->id() . "_" . $id, $control, $type, $params);
	}

	private function _parseField($id, $control, $type = null, $field = [])
	{
		if ($control == 'markup') {
			$field['control'] = $field['value'];
			return $this->fields[$id] = $field;
		}
		$build = "";
		$group = null;

		$field['attr']['id'] = $id;
		if (!is_null($type))
			$field['attr']['type'] = $type;
		if (isset($field['attr']['class']) and !is_array($field['attr']['class']))
			$field['attr']['class'] = [$field['attr']['class']];

		if ($control != 'button') {
			$field['attr']['name'] = (!is_array($field['value'])) ? $id : $id . "[]";
			$field['attr']['class'][] = "form-control";
			if (!isset($field['sanitize']))
				$field['sanitize'] = ['strip' => FILTER_FLAG_ENCODE_LOW];
		}
		if (isset($field['sanitize']))
			$this->sanitized[$id] = $field['sanitize'];

		if (isset($field['validate'])) {
			$this->validated[$id] = $field['validate'];
			foreach ($field['validate'] as $rule => $params)
				if (is_array($params) and array_intersect([Validation::error__, Validation::success__], array_keys($params)))
					$field['verbose'] = true;
		}
		if (is_array($field['value']))
			$this->_parseArrayField($id, $control, $field['value'], $field);

		switch ($control) {
			case 'input':
				if ($type == 'hidden')
					$group = $type;
				(!is_array($field['control'])) ?
					$field['attr']['value'] = $field['value'] :
					$group = $id;
				break;
			case 'select':
				$options = [];
				foreach ($field['control'] as $option)
					$options[] = $option['control'];
				$build = eol__ . implode(eol__, $options) . eol__ . "</" . $control . ">";
				break;
			case 'button':
				$build = $field['label'] . "</" . $control . ">";
				$field['label'] = "";
				$group = 'action';
				break;
			case 'textarea':
				$build = "</" . $control . ">";
				break;
		}
		$build = "<" . $control . Html::getAttr($field['attr']) . ">" . $build;

		if (!is_null($group))
			$id = $group;

		switch ($group) {
			case 'hidden':
			case 'action':
				$field['control'] = $build;
				$this->fields[$id]['control'][] = $field['control'];
				break;
			case null:
				$field['control'] = $build;
			default:
				$this->fields[$id] = $field;
		}
		return $this->fields[$id];
	}

	private function _parseArrayField($id, $control, &$values, &$parent)
	{
		$fields = $values;
		$values = [];

		foreach ($fields as $index => &$field) {

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
			$values[] = $field['value'];

			if (!isset($field['label']))
				$field['label'] = "";
			if (!isset($field['attr']))
				$field['attr'] = [];
			if (!is_array($field['attr']))
				$field['attr'] = [$field['attr']];

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
					$field['attr'] = Helper::getAttr(array_merge($parent['attr'], $field['attr']));
					$field['active'] = (isset($field['attr']['checked'])) ? true : false;
			}
			$build = "<" . $field['control'] . Html::getAttr($field['attr']) . ">" . $build;
			$field['control'] = $build;
		}
		unset($field);
		$parent['control'] = $fields;
	}

}
