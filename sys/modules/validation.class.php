<?php

namespace sys\modules;

class Validation extends \sys\Common {

	private $result = array();

	function __construct()
	{
		parent::__construct();
	}

	public function get($key = null)
	{
		if ($key) {
			if (isset($this->result[$key])) {
				return $this->result[$key];
			}
			return false;
		}
		return $this->result;
	}

	public function set($msg = null, $key = 'error', $subj = 'parse')
	{
		$this->result[$key]['validate'][] = array($subj, $msg);
	}

	public function validate($id, $validation, $value = null)
	{
		foreach ($validation as $key => $args) {
			$rule = (!is_numeric($key)) ? $key : $args;
			$param = (isset($args['value'])) ? $args['value'] : "";
			$valid = ($this->valid($rule, $value, $param)) ? 'success' : 'error';

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

	public function sanitize($value, $filter = 'string')
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

	public function valid($rule, $value = null, $param = null)
	{
		switch($rule) {
			case 'header':
				return ($value === $this->xhr->header());
			break;
			case 'match':
				return ($value === $param);
			break;
			case 'required':
				if (is_array($value)) {
					$value = array_values($value);
					$value = implode($value);
				}
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

}
