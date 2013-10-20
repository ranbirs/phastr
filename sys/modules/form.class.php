<?php

namespace sys\modules;

use sys\Init;
use sys\modules\Validation;
use sys\utils\Helper;
use sys\utils\Hash;
use sys\utils\Html;

abstract class Form {

	protected $form_id, $method, $import, $request, $validation;
	private $_field = [], $_build = [], $_fields = [];
	private $_validated = [], $_sanitized = [];
	private $_expire, $_success, $_fail, $_error;

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

		foreach ($this->_sanitized as $id => $filter)
			$this->requet->$method($id, $this->validation->sanitize($filter, $this->ajax->$method($id)));
		foreach ($this->_validated as $id => $validation)
			$this->validation->resolve($id, $validation, $this->request->$method($id));

		if (array_key_exists(Validation::error__, $this->validation->get())) {
			if (!isset($this->_error))
				$this->error();
			$this->_error['validation'] = $this->validation->get();
			return $this->_error;
		}
		if ($this->resolve($this->request->fields($this->form_id, $this->method), $this->import)) {
			if ((isset($this->_expire)) ? $this->_expire : $this->expire())
				Init::session()->drop($this->form_id, 'token');
			return (isset($this->_success)) ? $this->_success : $this->success();
		}
		return (isset($this->_fail)) ? $this->_fail : $this->fail();
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
		$this->_build['title'] = $title;
		$this->_build['attr'] = Html::getAttr($attr);

		$form = ['build' => $this->_build, 'fields' => $this->_fields];
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
		return $this->_error = ['result' => false, 'callback' => $callback, 'message' => $msg];
	}

	protected function fail($msg = "", $callback = "")
	{
		return $this->_fail = ['result' => false, 'callback' => $callback, 'message' => $msg];
	}

	protected function success($msg = "", $callback = "")
	{
		return $this->_success = ['result' => true, 'callback' => $callback, 'message' => $msg];
	}

	protected function expire($expire = true)
	{
		return $this->_expire = (bool) $expire;
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

		$this->_field = $params;

		return $this->_buildField($this->form_id . "_" . $id, $control, $type);
	}

	private function _buildField($id, $control, $type = null)
	{
		if ($control == 'markup') {
			return $this->_fields[$id]['field'] = $this->_field['value'];
		}
		$build = "";
		$group = null;

		$this->_field['attr']['id'] = $id;
		$this->_field['attr']['name'] = (!is_array($this->_field['value'])) ? $id : $id . "[]";
		if (!is_null($type))
			$this->_field['attr']['type'] = $type;

		if (isset($this->_field['attr']['class']) and !is_array($this->_field['attr']['class']))
			$this->_field['attr']['class'] = [$this->_field['attr']['class']];

		if ($control != 'button') {
			$this->_field['attr']['class'][] = "field";
			$this->_field['attr']['class'][] = "form-control";
		}
		if (isset($this->_field['sanitize']))
			$this->_sanitized[$id] = $this->_field['sanitize'];

		if (isset($this->_field['validate'])) {
			$this->_validated[$id] = $this->_field['validate'];
			foreach ($this->_field['validate'] as $rule => $params)
				if (is_array($params) and array_intersect([Validation::error__, Validation::success__], array_keys($params)))
					$this->_fields[$id]['verbose'] = true;
		}
		if (is_array($this->_field['value']))
			$this->_buildArrayField($id, $control);

		switch ($control) {
			case 'input':
				switch ($type) {
					case 'hidden':
						$group = $type;
						break;
				}
				(!is_array($this->_field['value'])) ?
					$this->_field['attr']['value'] = $this->_field['value'] :
					$group = $id;
				break;
			case 'select':
				$options = [];
				foreach ($this->_fields[$id]['field'] as $field)
					$options[] = $field['field'];
				$build = eol__ . implode(eol__, $options) . eol__ . "</" . $control . ">";
				break;
			case 'button':
				$build = $this->_field['label'] . "</" . $control . ">";
				$this->_field['label'] = null;
				$group = 'action';
				break;
			case 'textarea':
				$build = "</" . $control . ">";
				break;
		}
		$build = "<" . $control . Html::getAttr($this->_field['attr']) . ">" . $build;

		if (!is_null($group))
			$id = $group;

		if ($this->_field['label'])
			$this->_fields[$id]['label'] = $this->_field['label'];
		if (isset($this->_field['append']))
			$this->_fields[$id]['append'] = $this->_field['append'];
		if (isset($this->_field['prepend']))
			$this->_fields[$id]['prepend'] = $this->_field['prepend'];

		$this->_fields[$id]['type'] = $type;
		$this->_fields[$id]['control'] = $control;

		switch ($group) {
			case null:
				return $this->_fields[$id]['field'] = $build;
				break;
			case 'hidden':
			case 'action':
				return $this->_fields[$id]['field'][] = $build;
				break;
		}
		return $this->_fields[$id]['field'];
	}

	private function _buildArrayField($id, $control)
	{
		foreach ($this->_field['value'] as $index => &$field) {

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
					$build = $field['label'] . "</" . $field['control'] . ">";
					break;
				default:
					$field['attr']['id'] = $id . "-" . $index;
					$field['attr']['name'] = $id . "[]";
					$field['attr'] = array_merge($this->_field['attr'], $field['attr']);
			}
			$build = "<" . $field['control'] . Html::getAttr($field['attr']) . ">" . $build;

			$this->_fields[$id]['field'][] = [
				'label' => $field['label'],
				'field' => $build
			];
		}
		unset($field);
	}

}
