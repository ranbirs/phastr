<?php

namespace sys\modules;

use sys\Loader;
use app\configs\Validation as __validation;

class Validation
{

    use Loader;

    protected $result = [];

    public function getResult($status = null)
    {
        return (!$status) ? $this->result : ((isset($this->result[$status])) ? $this->result[$status] : false);
    }

    public function getStatus($id, $status = __validation::error__)
    {
        return (isset($this->result[$status][$id])) ? $this->result[$status][$id] : false;
    }

    public function setStatus($id, $status = __validation::error__, $message = null)
    {
        $this->result[$status][$id] = ['id' => $id, 'status' => $status, 'message' => $message];
    }

    public function resolve($id, $value = null, $rule = null, $message = null)
    {
        if (is_array($rule)) {
            $params = current($rule);
            $rule = key($rule);
        }
        $status = ($this->validate($value, $rule, ((isset($params)) ? $params : null))) ? __validation::success__ : __validation::error__;

        if (!is_array($message)) {
            $message = [__validation::error__ => $message];
        }
        if (array_key_exists($status, $message)) {
            $this->setStatus($id, $status, $message[$status]);
        } elseif ($status === __validation::error__) {
            $this->setStatus($id, $status);
        }
    }

    // @todo contextual param (explicit request, etc)
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
                return (isset($value) && $value === $request && $request === current($params));
            case 'request':
                return ($this->load()->module('request')->header($this->load()->module('session')->token()) === $this->load()->module('session')->get('_request'));
            case 'session':
                return (isset($params) && $value === $this->load()->module('session')->get($params));
            case 'compare':
                return (isset($params) && strcmp($value, $params) == 0);
            case 'require':
                if (is_array($value)) {
                    $value = implode($value);
                }
                return (strlen($value) > 0);
            case 'maxlength':
                if (!is_array($value)) {
                    return (strlen($value) < (int)$params);
                }
                foreach ($value as $val) {
                    if (strlen($val) > (int)$params) {
                        return false;
                    }
                }
                return true;
            case 'minlength':
                if (!is_array($value)) {
                    return (strlen($value) >= (int)$params);
                }
                foreach ($value as $val) {
                    if (strlen($val) < (int)$params) {
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
        unset($val);
        return $value;
    }

    public function filter($value = null, $rule = null, $params = null)
    {
        $value = trim($value);
        if (isset($params)) {
            return filter_var($value, $rule, $params);
        }
        return filter_var($value, $rule);
    }

}
