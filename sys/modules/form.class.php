<?php

namespace sys\modules;

class Form {

	protected $xhr, $validation;

	private $fid, $token, $html;

	private $build = array();
	private $fields = array();
	private $field = array();
	private $required = array();
	private $validated = array();
	private $sanitized = array();

	function __construct()
	{
		$this->xhr = \sys\Init::xhr();

		$this->fid = strtolower(\sys\utils\Helper::getClassName(get_class($this)));
		$this->token = \sys\Session::get($this->fid, 'token');
		if (!$this->token)
			$this->token = \sys\Session::set($this->fid, 'token');
	}

	public function html($data = null, $method = 'post', $template = "bootstrap/form")
	{
		$this->build($data);

		if ($this->xhr->request($method)) {
			$this->xhr->response($this->_submit($method));
		}

		if (!$this->html) {
			$this->build['method'] = $method;
			$action = "/" . \sys\Init::res('route') . "/" . \app\confs\sys\xhr_param__ .
				"/form/$method/" . $this->fid . "/";
			$this->build['action'] = $action;
			$data = array('build' => $this->build, 'fields' => $this->fields);
			$this->html = \sys\Init::view()->template('form', $template, $data);
		}
		return $this->html;
	}

	private function _submit($method)
	{
		$this->validation = new \sys\modules\Validation();

		foreach ($this->sanitized as $id => $filter) {
			$this->xhr->$method($id, $this->validation->sanitize($filter, $this->xhr->$method($id)));
		}
		foreach ($this->validated as $id => $validation) {
			$this->validation->parse($id, $validation, $this->xhr->$method($id));
		}
		if (array_key_exists('error', $this->validation->get())) {
			return $this->fail();
		}
		$parse = $this->parse();
		if ($parse['output']) {
			\sys\Session::drop($this->fid, 'token');

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
		$this->build['fid'] = $this->fid;
		$this->build['css'] = implode(" ", $css);
		$this->build['title'] = $title;
	}

	protected function close()
	{
		$key = \sys\Session::key();

		$this->field(array('input' => 'hidden'), "form_xid_$key", null,
			$data = array(
				'value' => \sys\Session::xid(),
				'validate' => array('xhr')
			)
		);
		$this->field(array('input' => 'hidden'), "form_fid_$key", null,
			$data = array(
				'value' => $this->fid,
				'validate' => array('match' => array('value' => $this->fid))
			)
		);
		$this->field(array('input' => 'hidden'), "form_token_$key", null,
			$data = array(
				'value' => $this->token,
				'validate' => array('match' => array('value' => $this->token))
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
		$this->field = $data;
		$this->field['label'] = $label;

		if (!isset($this->field['value']))
			$this->field['value'] = "";

		if (!isset($this->field['validate']))
			$this->field['validate'] = array();
		if (!isset($this->field['sanitize']))
			$this->field['sanitize'] = array();

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

		$this->field['stack'] = false;
		$this->field['attr']['id'] = $id;
		$this->field['attr']['name'] = (!is_array($this->field['value'])) ? $id : $id . "[]";
		$this->field['css'][] = "field";

		if ($control == 'input') {
			$this->field['attr']['type'] = $type;
			$this->field['attr']['value'] = $this->field['value'];
		}

		if (isset($this->field['prop'])) {
			foreach ($this->field['prop'] as $prop) {
				$this->field['attr'][$prop] = $prop;
			}
		}

		if (!empty($this->field['validate']))
			$this->_buildFieldValidation($id, $control, $type);

		if (is_array($this->field['value']))
			$this->_buildFieldStack($id, $control);

		$this->field['attr']['class'] = implode(" ", $this->field['css']);
		$build = "<$control" . \sys\utils\Html::getAttr($this->field['attr']) . ">";

		switch ($control) {
			case 'input':
				if ($type == 'hidden') {
					$this->field['stack'] = true;
					$key = $type;
				}
				if (is_array($this->field['value']))
					$this->field['stack'] = true;
			break;
			case 'select':
				$options = array();
				foreach ($this->fields[$id]['field'] as $field) {
					$options[] = $field['field'];
				}
				$build .= "\n\t" . implode("\n\t", $options) . "</$control>";
			break;
			case 'button':
				$build .= $this->field['label'] . "</$control>";
				if (in_array($type, array('submit', 'action'))) {
					$this->field['stack'] = true;
					$key = 'action';
				}
			break;
			case 'textarea':
				$build .= "</$control>";
			break;
			case 'markup':
				$build = $this->field['value'];
			break;
			default:
				return false;
			break;
		}

		if ($this->field['label'] and $control != 'button')
			$this->fields[$id]['label'] = $this->field['label'];

		if (isset($this->field['helptext']))
			$this->fields[$id]['helptext'] = $this->field['helptext'];

		if ($this->field['stack']) {
			return $this->fields[$key][] = $build;
		}

		return $this->fields[$key]['field'] = $build;
	}

	private function _buildFieldStack($id, $control)
	{
		$tag = $control;
		$build = "";

		foreach ($this->field['value'] as $index => &$field) {

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
				$field['css'] = array_merge($this->field['css'], $field['css']);
				$field['attr']['class'] = implode(" ", $field['css']);
				$field['attr']['id'] = $id . "-" . $index;
				$field['attr']['name'] = $id . "[]";
				$field['attr'] = array_merge($this->field['attr'], $field['attr']);
			}
			$this->fields[$id]['field'][] = array(
				'label' => $field['label'],
				'field' => "<$tag" . \sys\utils\Html::getAttr($field['attr']) . ">$build"
			);
		}
		unset($field);
	}

	private function _buildFieldValidation($id, $control, $type = null)
	{
		$this->validated[$id] = $this->field['validate'];
		$this->field['css'][] = "validated";

		foreach ($this->field['validate'] as $rule => $validation) {
			if (is_numeric($rule))
				$rule = $validation;

			if ($rule == 'required')
				$this->required[] = $id;
			$this->field['css'][] = "rule-" . $rule;

			if (is_array($validation)) {
				if ($rule == 'maxlength')
					$this->field['attr'][$rule] = $validation['value'];

				if (array_intersect(array('error', 'success'), array_keys($validation))) {
					if ($type != 'hidden')
						$this->fields[$id]['helper'] = true;
				}
			}
		}
	}

}
