<?php

namespace app\init;

use sys\Route;

class Mvc extends \sys\Init
{

	function __construct(Route $route)
	{
		parent::__construct($route);
		
		if (!$this->route->route['resource']) {
			$this->route->error(404, 'app/views/layouts/error/404');
		}
		$this->controller = $this->route->resource(true);
		
		if (method_exists($this->controller = new $this->controller(), $action = $this->route->action('-', '_'))) {
			$this->controller->init($action, $this->route->route['params']);
			$this->controller->{$action}($this->route->route['params']);
			$this->controller->render($action, $this->route->route['params']);
		}
		$this->route->error(404, 'app/views/layouts/error/404');
	}

}
