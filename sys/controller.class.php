<?php

namespace sys;

use sys\components\Compositor;

abstract class Controller extends Compositor {

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
			$this->view->error(404, \app\vocabs\sys\er_ccm__);
		}
		if (isset($params[2]) and $params[0] === \app\confs\sys\xhr_param__) {
			if ($this->xhr->header() === $this->session->xid())
				$this->_request($params[1], $params[2], ((isset($params[3])) ? $params[3] : 'json'));
		}
		$this->render($page, $action, $params);
	}

	private function _request($type, $subj, $context)
	{
		switch ($type) {
			case 'get':
			case 'post':
				switch ($context) {
					case 'json':
					case 'html':
						$request = $this->xhr->$type();
						if (!empty($request)) {
							$this->view->request = $request;
							$this->xhr->response($this->view->request($type, $subj), $context);
						}
						break 2;
				}
				break;
			case 'form':
				switch ($context) {
					case 'post':
					case 'get':
						if ($this->$subj instanceof \sys\modules\Form) {
							$request = $this->xhr->$context();
							if (!empty($request)) {
								$this->view->request = $request;
								$this->xhr->response($this->$subj->submit($context));
							}
						}
						break 2;
				}
		}
	}


}
