<?php

namespace sys;

use sys\modules\Request;

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

	public function dispatch($default, $page, $action, $params = [])
	{
		$dispatch = [
			$default,
			$default . '_' . $action,
			$page . '_' . $default,
			$page . '_' . $action
		];
		$render = false;

		foreach ($dispatch as $method) {
			if (method_exists($this, $method)) {
				$this->{$method}($page, $action, $params);
				$render = true;
			}
		}
		if (!$render) {
			$this->route()->error(404);
		}
		if ($this->request()->resolve($params)) {
			$this->view()->layout(['request', $this->request()->layout]);
		}
		$this->render($page, $action, $params);
	}

	public function render()
	{
		if (($this->view()->page = $this->view()->page()) !== false) {
			$this->view()->layout();
		}
		$this->route()->error(404);
	}

}
