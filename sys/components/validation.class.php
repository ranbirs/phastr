<?php

namespace sys\components;

class Validation {

	private $_result = array();

	function __construct()
	{

	}

	public function get($key = null)
	{
		return (!is_null($key) and isset($this->_result[$key])) ? $this->_result[$key] : $this->_result;
	}

	public function set($msg = null, $key = 'error', $subj = 'parse')
	{
		$this->_result[$key]['validate'][] = array($subj, $msg);
	}

	public function resolve($id, $validation, $value = null)
	{
		foreach ($validation as $key => $args) {
			$rule = (!is_int($key)) ? $key : $args;
			$param = (isset($args['value'])) ? $args['value'] : null;
			$valid = ($this->validate($rule, $value, $param)) ? 'success' : 'error';

			if (is_array($args)) {
				if (array_key_exists($valid, $args)) {
					$this->set($args[$valid], $valid, $id);
					continue;
				}
			}
			switch ($valid) {
				case 'error':
					$this->set("", $valid, $id);
					break;
			}
		}
	}

	public function validate($rule, $value = null, $param = null)
	{
		switch($rule) {
			case 'header':
			case 'server':
			case 'post':
			case 'get':
				if (!is_array($param)) {
					$valid = false;
					break;
				}
				$request = \sys\Init::xhr()->$rule(key($param));
				$valid = (!is_null($value) and $value === $request and $request === current($param));
				break;
			case 'token':
				$valid = (!is_null($param) and $value === \sys\Init::session()->get($param, 'token'));
				break;
			case 'match':
				$valid = (!is_null($param) and $value == $param);
				break;
			case 'required':
				if (is_array($value))
					$value = implode($value);
				$valid = (strlen($value) > 0);
				break;
			case 'maxlength':
				$param = (int) $param;
				if (!is_array($value)) {
					$valid = (strlen($value) < $param);
					break;
				}
				foreach ($value as $val) {
					if (strlen($val) > $param) {
						$valid = false;
						break 2;
					}
				}
				$valid = true;
				break;
			case 'alnum':
				$valid = (ctype_alnum($value));
				break;
			case 'email':
				$valid = (filter_var($value, FILTER_VALIDATE_EMAIL));
				break;
			default:
				$valid = true;
		}
		return $valid;
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
		}
		return $value;
	}

}
