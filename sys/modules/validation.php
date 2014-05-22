<?php

namespace sys\modules;

class Validation
{
	
	use \sys\Loader;

	const error__ = 'error';

	const success__ = 'success';

	protected $result = [];

	public function getResult($status = null)
	{
		return (!is_null($status)) ? ((isset($this->result[$status])) ? $this->result[$status] : null) : $this->result;
	}

	public function setStatus($id, $msg = null, $status = self::error__)
	{
		$this->result[$status][] = ['id' => $id, 'message' => $msg];
	}

	public function resolve($id, $validation, $value = null)
	{
		foreach ($validation as $rule => $args) {
			
			$rule = (!is_int($rule)) ? $rule : $args;
			$params = (isset($args['value'])) ? $args['value'] : $args;
			$status = ($this->validate($value, $rule, $params)) ? self::success__ : self::error__;
			
			if (is_array($args)) {
				if (array_key_exists($status, $args)) {
					$this->setStatus($id, $args[$status], $status);
					continue;
				}
			}
			if ($status === self::error__) {
				$this->setStatus($id, '', $status);
			}
		}
	}

	public function validate($value = null, $rule = null, $params = null)
	{
		switch ($rule) {
			case 'header':
			case 'server':
			case 'post':
			case 'get':
				if (!is_array($params)) {
					return false;
				}
				$request = $this->load()->module('request')->{$rule}(key($params));
				return (!is_null($value) && $value === $request && $request === current($params));
			case 'token':
				return (!is_null($params) && $value === $this->load()->module('session')->get($params, 'token'));
			case 'compare':
				return (!is_null($params) && strcmp($value, $params) == 0);
			case 'require':
				if (is_array($value)) {
					$value = implode($value);
				}
				return (strlen($value) > 0);
			case 'maxlength':
				$params = (int) $params;
				if (!is_array($value)) {
					return (strlen($value) < $params);
				}
				foreach ($value as $val) {
					if (strlen($val) > $params) {
						return false;
					}
				}
				return true;
			case 'minlength':
				$params = (int) $params;
				if (!is_array($value)) {
					return (strlen($value) > $params);
				}
				foreach ($value as $val) {
					if (strlen($val) < $params) {
						return false;
					}
				}
				return true;
			case 'email':
				return (filter_var($value, FILTER_VALIDATE_EMAIL) !== false);
			case 'url':
				return (filter_var($value, FILTER_VALIDATE_URL) !== false);
			case 'ip':
				return (filter_var($value, FILTER_VALIDATE_IP, $params) !== false);
			case 'regexp':
				return (filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => [$rule => $params]]) !== false);
			case 'int':
				return (filter_var($value, FILTER_VALIDATE_INT, ['options' => $params]) !== false);
			case 'float':
				return (filter_var($value, FILTER_VALIDATE_FLOAT) !== false);
			case 'alpha':
				return ctype_alpha($value);
			case 'alnum':
				return ctype_alnum($value);
			case null:
				return true;
			default:
				return false;
		}
	}

	public function sanitize($value = null, $rule = null, $params = null)
	{
		if (is_array($rule)) {
			$params = current($rule);
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
			return $value = $this->filter($value, $rule, $params);
		}
		foreach ($value as &$val) {
			$val = $this->filter($val, $rule, $params);
		}
		return $value;
	}

	public function filter($value = null, $rule = null, $params = null)
	{
		$value = trim($value);
		if (!is_null($params)) {
			return filter_var($value, $rule, $params);
		}
		return filter_var($value, $rule);
	}

}
