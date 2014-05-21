<?php

namespace sys;

abstract class Controller
{
	
	use \sys\traits\Route;
	use \sys\traits\View;
	use \sys\traits\Load;

	public function init($route)
	{
		$page = $route->page();
		$action = $route->action();
		$params = $route->params();
		
		$render = false;
		foreach ($route->action(true) as $method) {
			if (method_exists($this, $method)) {
				$this->{$method}($page, $action, $params);
				$render = true;
			}
		}
		if ($render) {
			$this->render();
		}
		$route->error(404);
	}

	public function render()
	{
		$this->view()->layout();
	}

}
