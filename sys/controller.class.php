<?php

namespace sys;

use sys\Init;
use sys\modules\Assets;
use sys\utils\Helper;

abstract class Controller {

	use \sys\traits\Loader;
	use \sys\traits\Access;
	use \sys\traits\Request;

	protected $view;

	function __construct()
	{
		$this->view = Init::view();
		$this->view->assets = new Assets();
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
			$this->view->error(404, \sys\confs\error\controller_methods__);
		}
		if (current($params) === \sys\modules\Request::param__) {
			if (!$this->submitRequest((isset($params[1])) ? $params[1] : null, (isset($params[2])) ? $params[2] : null)) {
				$this->view->error(404, \sys\confs\error\controller_request__);
			}
		}
		$this->render($page, $action, $params);
	}

	public function render()
	{
		$this->view->page = $this->view->page();
		$this->view->layout(\app\confs\config\layout__);
	}

}
