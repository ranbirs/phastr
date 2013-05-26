<?php

namespace sys\modules;

class Validation {

	private $_result = array();

	function __construct()
	{

	}

	public function get($key = null)
	{
		if ($key) {
			if (isset($this->_result[$key])) {
				return $this->_result[$key];
			}
			return false;
		}
		return $this->_result;
	}

	public function set($msg = null, $key = 'error', $subj = 'parse')
	{
		$this->_result[$key]['validate'][] = array($subj, $msg);
	}

	public function parse($id, $validation, $value = null)
	{
		foreach ($validation as $key => $args) {
			$rule = (!is_numeric($key)) ? $key : $args;
			$param = (isset($args['value'])) ? $args['value'] : "";
			$valid = ($this->validate($rule, $value, $param)) ? 'success' : 'error';

			if (is_array($args)) {
				if (array_key_exists($valid, $args)) {
					$this->set($args[$valid], $valid, $id);
					continue;
				}
			}
			if ($valid == 'error')
				$this->set("", $valid, $id);
		}
	}

	public function validate($rule, $value = null, $param = null)
	{
		switch($rule) {
			case 'xhr':
				return ($value === \sys\Res::xhr()->token());
			break;
			case 'match':
				return ($value === $param);
			break;
			case 'required':
				if (is_array($value))
					$value = implode($value);

				return ($value);
			break;
			case 'maxlength':
				if (!is_numeric($param)) {
					return false;
				}
				if (!is_array($value)) {
					return (strlen($value) < $param);
				}
				foreach ($value as $val) {
					if (strlen($val) > $param) {
						return false;
					}
				}
			break;
			case 'alnum':
				return (ctype_alnum($value));
			break;
			case 'email':
				return (filter_var($value, FILTER_VALIDATE_EMAIL));
			break;
			default:
				return false;
			break;
		}
		return true;
	}

	public function sanitize($filter = 'string', $value = null)
	{
		switch ($filter) {
			case 'string':
				$value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
			break;
			case 'int':
				$value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
			break;
			case 'float':
				$value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
			break;
			default:
				return false;
			break;
		}
		return $value;
	}

}
