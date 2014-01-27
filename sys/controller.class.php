<?php

namespace sys;

abstract class Controller {

	use \sys\traits\Route;
	use \sys\traits\Session;
	use \sys\traits\Load;
	use \sys\traits\View;
	use \sys\traits\Util;
	use \sys\traits\Request;

	function __construct()
	{

	}

	public function dispatch($methods, $page, $action, $params = [])
	{
		$render = false;
		foreach ((array) $methods as $method) {
			if (method_exists($this, $method)) {
				$this->{$method}($page, $action, $params);
				$render = true;
			}
		}
		if (!$render) {
			$this->route()->error(404);
		}
		$this->render(($this->request()->resolve($params)) ? ['request', $this->request()->layout] : null);
	}

	public function render($layout = null)
	{
		$this->view()->page = $this->view()->page();
		$this->view()->layout($layout);
	}

}
