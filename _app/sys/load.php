<?php

namespace sys;

class Load
{

    private $_instance;

    function __construct($instance)
    {
        $this->_instance = & $instance;
    }

    public function getInstance($path, $prop = null)
    {
        if (!isset($prop)) {
            $prop = basename($path);
        }
        if (!isset($this->_instance->{$prop})) {
            $class = '\\' . implode('\\', explode('/', $path));
            return $this->_instance->{$prop} = new $class();
        }
        return $this->_instance->{$prop};
    }

    public function init($subj)
    {
        if (!isset($this->_instance->{$subj})) {
            if (!isset(\sys\Init::$init->{$subj})) {
                $class = '\\sys\\' . $subj;
                \sys\Init::$init->{$subj} = new $class();
            }
            return $this->_instance->{$subj} = \sys\Init::$init->{$subj};
        }
        return $this->_instance->{$subj};
    }

    public function module($path, $base = sys__, $prop = null)
    {
        return $this->getInstance($base . '/modules/' . $path, $prop);
    }

    public function model($path, $prop = null)
    {
        return $this->getInstance('app/models/' . $path, $prop);
    }

    public function form($path, $prop = null)
    {
        return $this->getInstance('app/forms/' . $path, $prop);
    }

    public function nav($path, $prop = null)
    {
        return $this->getInstance('app/navs/' . $path, $prop);
    }

    public function service($path, $prop = null)
    {
        return $this->getInstance('app/services/' . $path, $prop);
    }

}
