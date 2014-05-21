<?php

namespace sys\modules;

class Validation
{
	
	use \sys\traits\Load;

	const error__ = 'error';

	const success__ = 'success';

	protected $result = [];

	public function getResult($status = null)
	{
		return (!is_null($status)) ? ((isset($this->result[$status])) ? $this->result[$status] : null) : $this->result;
	}

	public function setStatus($subj, $msg = null, $status = self::error__)
	{
		$this->result[$status][] = [$subj, $msg];
	}

	public function resolve($id, $validation, $value = null)
	{
		foreach ($validation as $key => $args) {
			
			$rule = (!is_int($key)) ? $key : $args;
			$param = (isset($args['value'])) ? $args['value'] : $args;
			$valid = ($this->validate($value, $rule, $param)) ? self::success__ : self::error__;
			
			if (is_array($args)) {
				if (array_key_exists($valid, $args)) {
					$this->setStatus($id, $args[$valid], $valid);
					continue;
				}
			}
			if ($valid === self::error__) {
				$this->setStatus($id, '', $valid);
			}
		}
	}

	public function validate($value = null, $rule = null, $param = null)
	{
		switch ($rule) {
			case 'header':
			case 'server':
			case 'post':
			case 'get':
				if (!is_array($param)) {
					return false;
				}
				$request = $this->load()->module('request')->{$rule}(key($param));
				return (!is_null($value) && $value === $request && $request === current($param));
			case 'token':
				return (!is_null($param) && $value === $this->load()->module('session')->get($param, 'token'));
			case 'match':
				return (!is_null($param) && strcmp($value, $param) == 0);
			case 'required':
				if (is_array($value)) {
					$value = implode($value);
				}
				return (strlen($value) > 0);
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
			case 'minlength':
				$param = (int) $param;
				if (!is_array($value)) {
					return (strlen($value) > $param);
				}
				foreach ($value as $val) {
					if (strlen($val) < $param) {
						return false;
					}
				}
				return true;
			case 'alpha':
				return ctype_alpha($value);
			case 'alnum':
				return ctype_alnum($value);
			case 'int':
				return (filter_var($value, FILTER_VALIDATE_INT, ['options' => $params]) !== false);
			case 'float':
				return (filter_var($value, FILTER_VALIDATE_FLOAT) !== false);
			case 'email':
				return (filter_var($value, FILTER_VALIDATE_EMAIL) !== false);
			case 'ip':
				return (filter_var($value, FILTER_VALIDATE_IP, $param) !== false);
			case 'regexp':
				return (filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => [$rule => $param]]) !== false);
			case null:
				return true;
			default:
				return false;
		}
	}

	public function sanitize($value = null, $rule = null, $param = null)
	{
		if (is_array($rule)) {
			$param = current($rule);
			$rule = key($rule);
		}
		switch ($rule) {
			case 'int':
				$rule = FILTER_SANITIZE_NUMBER_INT;
				break;
			case 'float':
				$rule = FILTER_SANITIZE_NUMBER_FLOAT;
				break;
			case 'strip':
			case 'string':
				$rule = FILTER_SANITIZE_STRING;
				break;
			case 'specialchars':
				$rule = FILTER_SANITIZE_SPECIAL_CHARS;
				break;
			case 'addslashes':
				$rule = FILTER_SANITIZE_MAGIC_QUOTES;
				break;
			case 'urlencode':
				$rule = FILTER_SANITIZE_ENCODED;
				break;
			case 'url':
				$rule = FILTER_SANITIZE_URL;
				break;
			case null:
				$rule = FILTER_UNSAFE_RAW;
				break;
			default:
				return false;
		}
		if (!is_array($value)) {
			return $value = $this->filter($value, $rule, $param);
		}
		foreach ($value as &$val) {
			$val = $this->filter($val, $rule, $param);
		}
		return $value;
	}

	public function filter($value = null, $rule = null, $param = null)
	{
		$value = trim($value);
		if (!is_null($param)) {
			return filter_var($value, $rule, $param);
		}
		return filter_var($value, $rule);
	}

}
