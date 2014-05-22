<?php

namespace sys;

abstract class Controller
{
	
	use \sys\Loader;

	function __construct()
	{
		$this->load()->init('route');
		$this->load()->init('view');
	}

	public function init($route)
	{
		$page = $route->page();
		$action = $route->action();
		$params = $route->params();
		
		$render = false;
		foreach ((array) $route->action(true) as $method) {
			if (method_exists($this, $method)) {
				$this->{$method}($page, $action, $params);
				$render = true;
			}
		}
		return $render;
	}

	public function render()
	{
		$this->view->layout();
	}

}
