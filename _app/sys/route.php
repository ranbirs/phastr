<?php

namespace sys;

use app\configs\Route as __route;

class Route
{

    public $path;
    
    function __construct()
    {
    	$this->path = $this->route($_SERVER['SCRIPT_NAME'], (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '');
    }

    public function route($file, $path)
    {
        $route['file'] = $file;
        $route['base'] = rtrim(dirname($route['file']), '/') . '/';
        $route['uri'] = trim($path, '/');
        ($route['uri']) ? $route['path'] = explode('/', $route['uri'], __route::limit__) : $route['uri'] = '/';

        if (!isset($route['path'][0])) {
            $route['path'][0] = __route::controller__;
        } elseif (!in_array($route['path'][0], explode(',', __route::scope__))) {
            return $this->error(404);
        }
        if (!isset($route['path'][1])) {
            $route['path'][1] = __route::action__;
        }
        $route['route'] = $route['path'];
        $route['params'] = array_splice($route['route'], 2);

        foreach ($route['route'] as &$arg) {
            if ((strlen($arg) > __route::length__) || preg_match('/[^a-z0-9-]/', $arg = strtolower($arg))) {
                return $this->error(404);
            }
            $route['label'][] = str_replace('-', '_', $arg);
        }
        unset($arg);

        $route['path'] = implode('/', $route['path']);
        $route['route'] = implode('/', $route['route']);

        $route['class'] = '\\app\\controllers\\' . $route['label'][0];
        $route['method'] = $route['label'][1] . __route::suffix__;

        return $route;
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
