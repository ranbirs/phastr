<?php

namespace sys;

class Init
{

	public static $init;

	function __construct()
	{
	    self::$init = &$this;

		$this->route = new \sys\Route();
		$this->controller = new $this->route->path['class']();

		if (method_exists($this->controller, $this->route->path['method'])) {
		    $this->controller->init($this->route->path['label'][1], $this->route->path['label'][2], $this->route->path['params']);
		    $this->controller->{$this->route->path['method']}($this->route->path['params']);
		    $this->controller->render();
		}
		$this->route->error(404);

		exit();
	}

}
