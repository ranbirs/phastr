<?php

namespace sys;

use sys\modules\Request;

abstract class Controller {

	use \sys\traits\Route;
	use \sys\traits\View;
	use \sys\traits\Load;
	use \sys\traits\Access;
	use \sys\traits\Request;

	function __construct()
	{

	}

	public function dispatch($default, $page, $action, $params = [])
	{
		$methods = [
			$default . "_" . $default,
			$default . "_" . $action,
			$page . "_" . $default,
			$page . "_" . $action
		];
		$process = count($methods);

		foreach ($methods as $method) {
			if (method_exists($this, $method)) {
				$this->$method($page, $action, $params);
				continue;
			}
			$process--;
		}
		if (empty($process)) {
			$this->route()->error(404, \sys\confs\error\controller_methods__);
		}
		$this->render($page, $action, $params);
	}

	public function render()
	{
		if ($this->route()->params(0) !== Request::param__) {
			$this->view()->page = $this->view()->page();
			if ($this->view()->page !== false) {
				$this->view()->layout();
			}
			$this->route()->error(404, \sys\confs\error\controller_render__);
		}
		if ($this->resolveRequest()) {
			$this->view()->layout(Request::param__ . "/" . $this->request()->layout);
		}
		$this->route()->error(404, \sys\confs\error\controller_request__);
	}

}
