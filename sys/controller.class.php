<?php

namespace sys;

use sys\components\Constructor;

abstract class Controller extends Constructor {

	function __construct()
	{
		parent::__construct();
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
			$request = $this->_request((isset($params[1])) ? $params[1] : null, (isset($params[2])) ? $params[2] : null);
			if (!$request) {
				$this->view->error(404, \sys\confs\error\controller_request__);
			}
			$this->view->response($request);
		}
		$this->render($page, $action, $params);
	}

	protected function render()
	{
		$this->view->layout(\app\confs\config\layout__);
	}

	private function _request($context = null, $subj = null)
	{
		if (is_null($context) or is_null($subj) or $this->request->header() !== $this->session->xid()) {
			return false;
		}
		switch ($context) {
			case 'request':
				$method = $this->request->method;
				$this->view->request = $this->request->$method();
				$this->view->response = $this->view->request($subj);
				return $layout = $this->request->layout;
			case 'form':
				if ($this->$context->$subj instanceof \sys\modules\Form) {
					$method = $this->$context->$subj->method();
					$this->view->request = $this->request->$method();
					$this->view->response = $this->$context->$subj->submit();
					return $layout = 'json';
				}
				break;
		}
		return false;
	}

}
