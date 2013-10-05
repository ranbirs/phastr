<?php

namespace sys\modules;

use sys\Init;
use sys\modules\Validation;
use sys\utils\Helper;
use sys\utils\Hash;
use sys\utils\Html;

abstract class Form {

	protected $request, $validation;
	private $_fid, $_method, $_import;
	private $_field = [], $_build = [], $_fields = [];
	private $_required = [], $_validated = [], $_sanitized = [];
	private $_expire, $_success, $_fail, $_error, $_html;

	function __construct()
	{
		$this->request = Init::request();
		$this->_fid = strtolower(Helper::getClassName(get_class($this)));

		if (!Init::session()->get($this->_fid, 'token'))
			Init::session()->set([$this->_fid => 'token'], Hash::rand());
	}

	abstract protected function build();

	public function fid()
	{
		return $this->_fid;
	}

	public function method()
	{
		return $this->_method;
	}

	public function submit()
	{
		$method = $this->_method;
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
		if ($this->resolve($this->request->fields($this->fid(), $this->method()), $this->_import)) {
			if ((isset($this->_expire)) ? $this->_expire : $this->expire())
				Init::session()->drop($this->_fid, 'token');
			return (isset($this->_success)) ? $this->_success : $this->success();
		}
		return (isset($this->_fail)) ? $this->_fail : $this->fail();
	}

	public function html($import = null, $title = null, $css = [], $method = 'post', $template = "bootstrap")
	{
		$this->_method = $method;
		$this->_import = $import;
		$this->build($import);
		$this->_close();

		if (!isset($this->_html)) {
			$this->_build['fid'] = $this->_fid;
			$this->_build['title'] = $title;
			$this->_build['css'] = implode(" ", $css);
			$this->_build['method'] = $method;
			$this->_build['action'] = Helper::getPath(['form', $this->_fid], 'ajax') . "/";
			$form = ['build' => $this->_build, 'fields' => $this->_fields];
			$this->_html = Init::view()->template('form', $template, $form);
		}
		return $this->_html;
	}

	private function _close()
	{
		$key = Init::session()->key();
		$xid = Init::session()->xid();

		$this->field(['input' => 'hidden'], "_fid_" . $key, null,
			$params = [
				'value' => $this->_fid,
				'validate' => [$this->_method => [$this->_fid . "__fid_" . $key => $this->_fid]]
			]
		);
		$this->field(['input' => 'hidden'], "_xid_" . $key, null,
			$params = [
				'value' => $xid,
				'validate' => ['header' => [$key => $xid]]
			]
		);
		$this->field(['input' => 'hidden'], "_token_" . $key, null,
			$params = [
				'value' => Init::session()->get($this->_fid, 'token'),
				'validate' => ['token' => $this->_fid]
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
		if ($control == 'input' and is_null($type))
			$type = 'text';

		if (!is_array($params))
			$params = ['value' => $params];

		if (!isset($params['value']))
			$params['value'] = (array_values($params) !== $params) ? "" : $params;
		$params['label'] = $label;

		$id = $this->_fid . "_" . $id;

		$this->_field = $params;

		return $this->_buildField($id, $control, $type);
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
		$this->_field['css'][] = "field";
		if ($control != 'button')
			$this->_field['css'][] = "form-control";

		if ($control == 'input')
			$this->_field['attr']['type'] = $type;

		if (isset($this->_field['prop']))
			foreach ($this->_field['prop'] as $prop)
				$this->_field['attr'][$prop] = $prop;

		if (isset($this->_field['sanitize']))
			$this->_sanitized[$id] = $this->_field['sanitize'];
		if (isset($this->_field['validate']))
			$this->_parseFieldValidation($id, $control, $type);

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
				$build = "\n\t" . implode("\n\t", $options) . "</" . $control . ">";
				break;
			case 'button':
				$build = $this->_field['label'] . "</" . $control . ">";
				$this->_field['label'] = null;
				switch ($type) {
					case 'submit':
						break;
					case 'button':
					case 'action':
					case null:
						$type = 'button';
						break;
				}
				$this->_field['attr']['type'] = $type;
				$group = 'action';
				break;
			case 'textarea':
				$build = "</" . $control . ">";
				break;
		}
		$this->_field['attr']['class'] = implode(" ", $this->_field['css']);
		$build = "<" . $control . Html::getAttr($this->_field['attr']) . ">" . $build;

		if (!is_null($group))
			$id = $group;

		if ($this->_field['label'])
			$this->_fields[$id]['label'] = $this->_field['label'];
		if (isset($this->_field['help']))
			$this->_fields[$id]['help'] = $this->_field['help'];

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
			if (!isset($field['css']))
				$field['css'] = [];

			$field['attr']['value'] = $field['value'];

			if (isset($field['prop']))
				foreach ($field['prop'] as $prop)
					$field['attr'][$prop] = $prop;

			$field['control'] = $control;
			$build = "";

			switch ($control) {
				case 'select':
					if (!empty($field['css']))
						$field['attr']['class'] = implode(" ", $field['css']);
					$field['control'] = 'option';
					$build = $field['label'] . "</" . $field['control'] . ">";
					break;
				default:
					$field['css'] = array_merge($this->_field['css'], $field['css']);
					$field['attr']['class'] = implode(" ", $field['css']);
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

	private function _parseFieldValidation($id, $control, $type = null)
	{
		$this->_validated[$id] = $this->_field['validate'];
		$this->_field['css'][] = "validated";

		foreach ($this->_field['validate'] as $rule => $params) {

			if (is_int($rule))
				$rule = $params;

			switch ($rule) {
				case 'required':
					$this->_required[] = $id;
					//$this->_field['attr'][$rule] = $rule;
					break;
				case 'maxlength':
						$this->_field['attr'][$rule] = (isset($params['value'])) ?
							(int) $params['value'] :
							((!is_array($params)) ? (int) $params : "");
					break;
			}
			$this->_field['css'][] = "rule-" . $rule;

			if (is_array($params) and $type != 'hidden')
				if (array_intersect([Validation::error__, Validation::success__], array_keys($params)))
					$this->_fields[$id]['verbose'] = true;
		}
	}

}
