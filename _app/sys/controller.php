<?php

namespace sys;

abstract class Controller
{

    use Loader;

    abstract public function init();

    abstract public function render();

}
