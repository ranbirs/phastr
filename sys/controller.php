<?php

namespace sys;

abstract class Controller
{
	
	use \sys\traits\Route;
	use \sys\traits\View;
	use \sys\traits\Load;

	function __construct()
	{
	}

	public function dispatch($route)
	{
		$page = $route->page();
		$action = $route->action();
		$params = $route->params();
		$methods = $route->action(true);
		$render = false;
		foreach ((array) $methods as $method) {
			if (method_exists($this, $method)) {
				$this->{$method}($page, $action, $params);
				$render = true;
			}
		}
		if (!$render) {
			$route->error(404);
		}
		$this->render();
	}

	public function render()
	{
		$this->view()->layout();
	}

}
