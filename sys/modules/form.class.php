<?php

namespace sys\modules;

use sys\Res;

use sys\modules\Validation;

use sys\utils\Html;
use sys\utils\Helper;

class Form {

	protected $xhr, $validation;

	private $_fid, $_token, $_html;

	private $_field = array();

	private $_build = array();
	private $_fields = array();

	private $_required = array();
	private $_validated = array();
	private $_sanitized = array();

	function __construct()
	{
		$this->xhr = Res::xhr();

		$this->_fid = strtolower(Helper::getClassName(get_class($this)));
		$this->_token = Res::session()->get($this->_fid, 'token');
		if (!$this->_token)
			$this->_token = Res::session()->set($this->_fid, 'token');
	}

	public function html($data = null, $method = 'post', $template = "bootstrap/form")
	{
		$this->build($data);

		if ($this->xhr->request($method)) {
			$this->xhr->response($this->_submit($method));
		}

		if (!$this->_html) {
			$this->_build['method'] = $method;
			$action = "/" . Res::get('route') . "/" . \app\confs\sys\xhr_param__ .
				"/form/$method/" . $this->_fid . "/";
			$this->_build['action'] = $action;
			$data = array('build' => $this->_build, 'fields' => $this->_fields);
			$this->_html = Res::view()->template('form', $template, $data);
		}
		return $this->_html;
	}

	private function _submit($method)
	{
		$this->validation = new Validation();

		foreach ($this->_sanitized as $id => $filter) {
			$this->xhr->$method($id, $this->validation->sanitize($filter, $this->xhr->$method($id)));
		}
		foreach ($this->_validated as $id => $validation) {
			$this->validation->parse($id, $validation, $this->xhr->$method($id));
		}
		if (array_key_exists('error', $this->validation->get())) {
			return $this->fail();
		}
		$parse = $this->parse();
		if ($parse['output']) {
			Res::session()->drop($this->_fid, 'token');

			return $this->success();
		}
		$this->validation->set($parse['message']);

		return $this->fail();
	}

	protected function build()
	{

	}

	protected function open($title = null, $css = array())
	{
		$this->_build['fid'] = $this->_fid;
		$this->_build['css'] = implode(" ", $css);
		$this->_build['title'] = $title;
	}

	protected function close()
	{
		$key = Res::session()->key();

		$this->field(array('input' => 'hidden'), "form_xid_$key", null,
			$data = array(
				'value' => Res::session()->xid(),
				'validate' => array('xhr')
			)
		);
		$this->field(array('input' => 'hidden'), "form_fid_$key", null,
			$data = array(
				'value' => $this->_fid,
				'validate' => array('match' => array('value' => $this->_fid))
			)
		);
		$this->field(array('input' => 'hidden'), "form_token_$key", null,
			$data = array(
				'value' => $this->_token,
				'validate' => array('match' => array('value' => $this->_token))
			)
		);
	}

	protected function parse()
	{
		return array('output' => true);
	}

	protected function success()
	{
		return array('callback' => "void(0)");
	}

	protected function fail()
	{
		return $this->validation->get();
	}

	protected function field($control, $id, $label = null, $data = array())
	{
		$type = null;
		$this->_field = $data;
		$this->_field['label'] = $label;

		if (!isset($this->_field['value']))
			$this->_field['value'] = "";

		if (!isset($this->_field['validate']))
			$this->_field['validate'] = array();
		if (!isset($this->_field['sanitize']))
			$this->_field['sanitize'] = array();

		if (is_array($control)) {
			$type = current(array_values($control));
			$control = current(array_keys($control));
		}

		$this->_buildField($id, $control, $type);
	}

	private function _buildField($id, $control, $type = null)
	{
		$key = $id;
		$build = "";

		$this->_field['stack'] = false;
		$this->_field['attr']['id'] = $id;
		$this->_field['attr']['name'] = (!is_array($this->_field['value'])) ? $id : $id . "[]";
		$this->_field['css'][] = "field";

		if ($control == 'input') {
			$this->_field['attr']['type'] = $type;
			$this->_field['attr']['value'] = $this->_field['value'];
		}

		if (isset($this->_field['prop'])) {
			foreach ($this->_field['prop'] as $prop) {
				$this->_field['attr'][$prop] = $prop;
			}
		}

		if (!empty($this->_field['validate']))
			$this->_buildFieldValidation($id, $control, $type);

		if (is_array($this->_field['value']))
			$this->_buildFieldStack($id, $control);

		$this->_field['attr']['class'] = implode(" ", $this->_field['css']);
		$build = "<$control" . Html::getAttr($this->_field['attr']) . ">";

		switch ($control) {
			case 'input':
				if ($type == 'hidden') {
					$this->_field['stack'] = true;
					$key = $type;
				}
				if (is_array($this->_field['value']))
					$this->_field['stack'] = true;
			break;
			case 'select':
				$options = array();
				foreach ($this->_fields[$id]['field'] as $field) {
					$options[] = $field['field'];
				}
				$build .= "\n\t" . implode("\n\t", $options) . "</$control>";
			break;
			case 'button':
				$build .= $this->_field['label'] . "</$control>";
				if (in_array($type, array('submit', 'action'))) {
					$this->_field['stack'] = true;
					$key = 'action';
				}
			break;
			case 'textarea':
				$build .= "</$control>";
			break;
			case 'markup':
				$build = $this->_field['value'];
			break;
			default:
				return false;
			break;
		}

		if ($this->_field['label'] and $control != 'button')
			$this->_fields[$id]['label'] = $this->_field['label'];

		if (isset($this->_field['helptext']))
			$this->_fields[$id]['helptext'] = $this->_field['helptext'];

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

			if ($control == 'select') {
				if (!empty($field['css']))
					$field['attr']['class'] = implode(" ", $field['css']);
				$tag = 'option';
				$build = $field['label'] . "</$tag>";
			}
			else {
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
			if (is_numeric($rule))
				$rule = $validation;

			if ($rule == 'required')
				$this->_required[] = $id;
			$this->_field['css'][] = "rule-" . $rule;

			if (is_array($validation)) {
				if ($rule == 'maxlength')
					$this->_field['attr'][$rule] = $validation['value'];

				if (array_intersect(array('error', 'success'), array_keys($validation))) {
					if ($type != 'hidden')
						$this->_fields[$id]['helper'] = true;
				}
			}
		}
	}

}
