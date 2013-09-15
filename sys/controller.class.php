<?php

namespace sys;

use sys\Init;
use sys\utils\Helper;

abstract class Controller {

	use \sys\traits\Loader;
	use \sys\traits\Access;
	use \sys\traits\Request;

	protected $view;

	function __construct()
	{
		$this->view = Init::view();
	}

	public function dispatch($default, $page, $action, $params = array())
	{
		$methods = array(
			$default . "_" . $default,
			$default . "_" . $action,
			$page . "_" . $default,
			$page . "_" . $action
		);
		$process = count($methods);
		foreach ($methods as $method) {
			if (method_exists($this, $method)) {
				$this->$method($page, $action, $params);
				continue;
			}
			$process--;
		}
		if (empty($process)) {
			$this->view->error(404, \sys\confs\error\controller_methods__);
		}
		if (current($params) === \sys\modules\Request::param__) {
			$request = $this->resolveRequest((isset($params[1])) ? $params[1] : null, (isset($params[2])) ? $params[2] : null);
			if (!$request) {
				$this->view->error(404, \sys\confs\error\controller_request__);
			}
			$this->view->response($request);
		}
		$this->render($page, $action, $params);
	}

	protected function render()
	{
		$this->view->page = $this->view->page();
		$this->view->layout(\app\confs\config\layout__);
	}

}
