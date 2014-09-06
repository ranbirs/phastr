<?php

namespace sys;

use app\configs\Route as __route;

class Route
{

    public $path;

    function __construct()
    {
        $path['file'] = $_SERVER['SCRIPT_NAME'];
        $path['base'] = rtrim(dirname($path['file']), '/') . '/';
        $path['uri'] = (isset($_SERVER['PATH_INFO'])) ? trim($_SERVER['PATH_INFO'], '/') : '';
        ($path['uri']) ? $path['path'] = explode('/', $path['uri'], __route::limit__) : $path['uri'] = '/';

        if (!isset($path['path'][0])) {
            $path['path'][0] = __route::controller__;
        } elseif (!in_array($path['path'][0], explode(',', __route::scope__))) {
            return $this->error(404);
        }
        if (!isset($path['path'][1])) {
            $path['path'][1] = __route::action__;
        }
        $path['route'] = $path['path'];
        $path['params'] = array_splice($path['route'], 2);

        foreach ($path['route'] as &$arg) {
            if ((strlen($arg) > __route::length__) || preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
                return $this->error(404);
            }
            $path['label'][] = str_replace('-', '_', $arg);
        }
        unset($arg);

        $path['path'] = implode('/', $path['path']);
        $path['route'] = implode('/', $path['route']);

        $path['class'] = '\\app\\controllers\\' . $path['label'][0];
        $path['method'] = $path['label'][1] . __route::suffix__;

        $this->path = $path;
    }

    public function uri()
    {
        return $this->path['uri'];
    }

    public function path($key = 'path')
    {
        return (isset($this->path[$key])) ? $this->path[$key] : false;
    }

    public function controller($class = false)
    {
        return (!$class) ? $this->path['label'][0] : $this->path['class'];
    }

    public function page()
    {
        return $this->path['label'][1];
    }

    public function action($method = false)
    {
        return (!$method) ? $this->path['label'][1] : $this->path['method'];
    }

    public function params($index = null)
    {
        return (!isset($index)) ? $this->path['params'] : ((isset($this->path['params'][$index])) ? $this->path['params'][$index] : false);
    }

    public function request($key = __route::request__)
    {
        return (isset($this->path['params'][1]) && $this->path['params'][0] == $key) ? $this->path['params'][1] : false;
    }

    public function error($code = 404, $message = null)
    {
        if ($message) {
            trigger_error($message);
        }
        $this->status($code);

        require app__ . '/views/layouts/error/' . $code . '.php';

        exit();
    }

    public function status($code = null)
    {
        return ($code) ? http_response_code($code) : http_response_code();
    }

}
