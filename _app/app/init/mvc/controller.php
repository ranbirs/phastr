<?php

namespace app\init\mvc;

use sys\Loader;

abstract class Controller
{

    use Loader;
    
    abstract public function init();

    abstract public function render();

}
