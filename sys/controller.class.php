<?php

namespace sys;

use sys\Res;
use sys\components\Compositor;

class Controller extends Compositor {

	protected $view, $load, $session, $xhr;

	function __construct()
	{
		parent::__construct();

		$this->view = Res::view();
		$this->load = Res::load();
		$this->session = Res::session();
		$this->xhr = Res::xhr();
	}

	final public function dispatch($default, $page, $action, $params = array())
	{
		$methods = array(
			$page . "_" . $action,
			$page . "_" . $default,
			$default . "_" . $action,
			$default . "_" . $default
		);
		foreach ($methods as $method) {
			if (method_exists($this, $method))
				$this->$method($page, $action, $params);
		}
		if (isset($params[2]) and $params[0] === \app\confs\sys\xhr_param__) {
			if ($this->xhr->header() === $this->session->xid())
				$this->_request($params[1], $params[2], ((isset($params[3])) ? $params[3] : 'json'));
		}
		if (method_exists($this, $default))
			$this->$default($page, $action, $params);
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
							$this->xhr->response($this->view->request("$type/$subj"), $context);
						}
						break 2;
				}
				break;
			case 'form':
				if ($this->$context instanceof \sys\modules\Form) {
					$request = $this->xhr->$subj();
					if (!empty($request)) {
						$this->view->request = $request;
						$this->xhr->response($this->$context->submit($subj));
					}
				}
				break;
		}
	}


}
