<?php

namespace sys\modules;

use sys\Init;
use sys\components\Validation;
use sys\utils\Helper;
use sys\utils\Hash;
use sys\utils\Html;

abstract class Form {

	protected $fid, $method, $xhr, $validation;
	private $_field = array(), $_build = array(), $_fields = array();
	private $_required = array(), $_validated = array(), $_sanitized = array(), $_expire = true;
	private $_html;

	function __construct()
	{
		$this->fid = strtolower(Helper::getClassName(get_class($this)));
		if (!Init::session()->get($this->fid, 'token'))
			Init::session()->set(array($this->fid => 'token'), Hash::rand());
		$this->xhr = Init::xhr();
	}

	abstract protected function build();

	final public function fid()
	{
		return $this->fid;
	}

	final public function method()
	{
		return $this->method;
	}

	final public function submit()
	{
		$method = $this->method;
		$this->validation = new Validation();

		foreach ($this->_sanitized as $id => $filter) {
			$this->xhr->$method($id, $this->validation->sanitize($filter, $this->xhr->$method($id)));
		}
		foreach ($this->_validated as $id => $validation) {
			$this->validation->resolve($id, $validation, $this->xhr->$method($id));
		}
		if (array_key_exists('error', $this->validation->get())) {
			return $this->fail();
		}
		$parse = $this->parse();
		if (isset($parse['result'])) {
			if ($parse['result']) {
				if ($this->_expire)
					Init::session()->drop($this->fid, 'token');
				return $this->success();
			}
			if (isset($parse['message'])) {
				if ($parse['message'])
					$this->validation->set($parse['message']);
			}
		}
		return $this->fail();
	}

	final public function html($data = null, $title = null, $css = array(), $method = 'post', $template = "bootstrap")
	{
		$this->method = $method;
		$this->build($data);
		$this->_close();

		if (!$this->_html) {
			$this->_build['fid'] = $this->fid;
			$this->_build['title'] = $title;
			$this->_build['css'] = implode(" ", $css);
			$this->_build['method'] = $method;
			$this->_build['action'] = Helper::getPath(array('form', $this->fid), 'xhr') . "/";
			$data = array('build' => $this->_build, 'fields' => $this->_fields);
			$this->_html = Init::view()->template('form', $template, $data);
		}
		return $this->_html;
	}

	private function _close()
	{
		$key = Init::session()->key();
		$xid = Init::session()->xid();

		$this->field(array('input' => 'hidden'), "fid_" . $key, null,
			$data = array(
				'value' => $this->fid,
				'validate' => array($this->method => array('value' => array($this->fid . "_fid_" . $key => $this->fid)))
			)
		);
		$this->field(array('input' => 'hidden'), "xid_" . $key, null,
			$data = array(
				'value' => $xid,
				'validate' => array('header' => array('value' => array($key => $xid)))
			)
		);
		$this->field(array('input' => 'hidden'), "token_" . $key, null,
			$data = array(
				'value' => Init::session()->get($this->fid, 'token'),
				'validate' => array('token' => array('value' => $this->fid))
			)
		);
	}

	protected function parse()
	{
		return array('result' => true, 'message' => "");
	}

	protected function success()
	{
		return array('callback' => "void(0)");
	}

	protected function fail()
	{
		return $this->validation->get();
	}

	protected function expire($expire = true)
	{
		$this->_expire = (bool) $expire;
	}

	protected function field($control, $id, $label = null, $data = array())
	{
		$id = $this->fid . "_" . $id;
		$type = null;
		if (is_array($control)) {
			$type = current($control);
			$control = key($control);
		}
		$this->_field = $data;
		$this->_field['label'] = $label;
		if (!isset($this->_field['value']))
			$this->_field['value'] = "";

		$this->_buildField($id, $control, $type);
	}

	private function _buildField($id, $control, $type = null)
	{
		$key = $id;
		$build = array_fill(0, 3, null);

		$this->_field['attr']['id'] = $id;
		$this->_field['attr']['name'] = (!is_array($this->_field['value'])) ? $id : $id . "[]";
		$this->_field['css'][] = "field";
		$this->_field['stack'] = false;

		if ($control == 'input') {
			$this->_field['attr']['type'] = $type;
			$this->_field['attr']['value'] = $this->_field['value'];
		}

		if (isset($this->_field['prop'])) {
			foreach ($this->_field['prop'] as $prop) {
				$this->_field['attr'][$prop] = $prop;
			}
		}

		if (!isset($this->_field['validate']))
			$this->_field['validate'] = array();
		if (!isset($this->_field['sanitize']))
			$this->_field['sanitize'] = array();

		if (!empty($this->_field['validate']))
			$this->_buildFieldValidation($id, $control, $type);

		if (is_array($this->_field['value']))
			$this->_buildFieldStack($id, $control);

		switch ($control) {
			case 'input':
				switch ($type) {
					case 'hidden':
						$this->_field['stack'] = true;
						$key = $type;
						break;
				}
				if (is_array($this->_field['value']))
					$this->_field['stack'] = true;
				break;
			case 'select':
				$options = array();
				foreach ($this->_fields[$id]['field'] as $field) {
					$options[] = $field['field'];
				}
				$build[1] = "\n\t" . implode("\n\t", $options) . "</$control>";
				break;
			case 'button':
				$build[1] = $this->_field['label'] . "</$control>";
				$this->_field['label'] = null;

				if (in_array($type, array('submit', 'action'))) {
					$this->_field['stack'] = true;
					$key = 'action';
				}
				unset($this->_fields[$id]['label']);
				break;
			case 'textarea':
				$build[1] = "</$control>";
				break;
			case 'markup':
				$build[2] = $this->_field['value'];
				break;
		}

		$this->_field['attr']['class'] = implode(" ", $this->_field['css']);

		$build[0] = (!$build[2]) ? "<$control" . Html::getAttr($this->_field['attr']) . ">" : null;
		$build = implode(array_filter($build));

		if ($this->_field['label'])
			$this->_fields[$id]['label'] = $this->_field['label'];

		if (isset($this->_field['hint']))
			$this->_fields[$id]['hint'] = $this->_field['hint'];

		if ($this->_field['stack']) {
			return $this->_fields[$key][] = $build;
		}
		return $this->_fields[$key]['field'] = $build;
	}

	private function _buildFieldStack($id, $control)
	{
		$tag = $control;
		$build = "";

		foreach ($this->_field['value'] as $index => &$field) {

			if (!isset($field['label']))
				$field['label'] = "";

			if (!isset($field['css']))
				$field['css'] = array();

			$field['attr']['value'] = $field['value'];

			if (isset($field['prop'])) {
				foreach ($field['prop'] as $prop) {
					$field['attr'][$prop] = $prop;
				}
			}

			switch ($control) {
				case 'select':
					if (!empty($field['css']))
						$field['attr']['class'] = implode(" ", $field['css']);
					$tag = 'option';
					$build = $field['label'] . "</$tag>";
					break;
				default:
					$field['css'] = array_merge($this->_field['css'], $field['css']);
					$field['attr']['class'] = implode(" ", $field['css']);
					$field['attr']['id'] = $id . "-" . $index;
					$field['attr']['name'] = $id . "[]";
					$field['attr'] = array_merge($this->_field['attr'], $field['attr']);
			}
			$this->_fields[$id]['field'][] = array(
				'label' => $field['label'],
				'field' => "<$tag" . Html::getAttr($field['attr']) . ">$build"
			);
		}
		unset($field);
	}

	private function _buildFieldValidation($id, $control, $type = null)
	{
		$this->_validated[$id] = $this->_field['validate'];
		$this->_field['css'][] = "validated";

		foreach ($this->_field['validate'] as $rule => $validation) {

			if (is_int($rule))
				$rule = $validation;

			switch ($rule) {
				case 'required':
					$this->_required[] = $id;
					break;
				case 'maxlength':
					if (isset($validation['value']))
						$this->_field['attr'][$rule] = (int) $validation['value'];
					break;
			}
			$this->_field['css'][] = "rule-" . $rule;

			if (is_array($validation)) {
				if (array_intersect(array('error', 'success'), array_keys($validation))) {
					switch ($type) {
						case 'hidden':
							continue 2;
						default:
							$this->_fields[$id]['help'] = true;
					}
				}
			}
		}
	}

}
