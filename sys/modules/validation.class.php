<?php

namespace sys\modules;

use sys\Init;

class Validation {

	const error__ = 'error';
	const success__ = 'success';

	private $_result = [];

	function __construct()
	{

	}

	public function get($status = null)
	{
		return (!is_null($status)) ?
			((isset($this->_result[$status])) ? $this->_result[$status] : null) :
			$this->_result;
	}

	public function set($subj, $msg = null, $status = self::error__)
	{
		$this->_result[$status][] = [$subj, $msg];
	}

	public function resolve($id, $validation, $value = null)
	{
		foreach ($validation as $key => $args) {
			$rule = (!is_int($key)) ? $key : $args;
			$param = (isset($args['value'])) ? $args['value'] : $args;
			$valid = ($this->validate($rule, $value, $param)) ? self::success__ : self::error__;

			if (is_array($args)) {
				if (array_key_exists($valid, $args)) {
					$this->set($id, $args[$valid], $valid);
					continue;
				}
			}
			if ($valid === self::error__)
				$this->set($id, "", $valid);
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
					return false;
				}
				$request = Init::request()->$rule(key($param));
				return (!is_null($value) and $value === $request and $request === current($param));
				break;
			case 'token':
				return (!is_null($param) and $value === Init::session()->get($param, 'token'));
				break;
			case 'match':
				return (!is_null($param) and $value == $param);
				break;
			case 'required':
				if (is_array($value))
					$value = implode($value);
				return (strlen($value) > 0);
				break;
			case 'maxlength':
				$param = (int) $param;
				if (!is_array($value)) {
					return (strlen($value) < $param);
				}
				foreach ($value as $val) {
					if (strlen($val) > $param) {
						return false;
					}
				}
				return true;
				break;
			case 'alnum':
				return (ctype_alnum($value));
				break;
			case 'email':
				return (filter_var($value, FILTER_VALIDATE_EMAIL));
				break;
			default:
				return false;
		}
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
		}
		return $value;
	}

}
