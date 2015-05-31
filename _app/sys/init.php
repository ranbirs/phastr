<?php

namespace sys;

abstract class Init
{

    public static $init;

    function __construct()
    {
        self::$init = &$this;
    }
    
}