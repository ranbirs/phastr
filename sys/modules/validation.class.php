<?php

namespace sys\modules;

use sys\Res;

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
				return ($value === Res::xhr()->token());
			case 'match':
				return ($value === $param);
			case 'required':
				if (is_array($value))
					$value = implode($value);
				return ($value);
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
				return true;
			case 'alnum':
				return (ctype_alnum($value));
			case 'email':
				return (filter_var($value, FILTER_VALIDATE_EMAIL));
		}
	}

	public function sanitize($filter = 'string', $value = null)
	{
		switch ($filter) {
			case 'string':
				return filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
			case 'int':
				return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
			case 'float':
				return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
		}
	}

}
