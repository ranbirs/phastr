<?php

namespace sys;

use sys\components\Constructor;

abstract class Controller extends Constructor {

	function __construct()
	{
		parent::__construct();
	}

	abstract protected function render();

	final public function dispatch($default, $page, $action, $params = array())
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
			$this->view->error(404, \app\vocabs\sys\error\controller_methods__);
		}
		if (isset($params[2]) and $params[0] === \app\confs\sys\xhr_param__) {
			if ($this->xhr->header() === $this->session->xid())
				$this->_xhr($params[1], $params[2]);
		}
		$this->render($page, $action, $params);
	}

	private function _xhr($context, $subj)
	{
		switch ($context) {
			case 'view':
				$method = (isset($this->view->request_method)) ? $this->view->request_method : 'post';
				$this->view->request = $this->xhr->$method();
				if (!empty($this->view->request)) {
					$format = (isset($this->view->request_format)) ? $this->view->request_format : 'json';
					$this->xhr->response($this->view->request($subj), $format);
				}
				break;
			case 'form':
				if ($this->$context->$subj instanceof \sys\modules\Form) {
					$method = $this->$context->$subj->method();
					$this->view->request = $this->xhr->$method();
					if (!empty($this->view->request)) {
						$this->xhr->response($this->$context->$subj->submit());
					}
				}
				break;
		}
	}

}
