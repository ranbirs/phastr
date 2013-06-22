<?php

namespace sys;

use sys\Res;
use sys\components\Compositor;
use sys\utils\Helper;

class Controller extends Compositor {

	protected $view, $load, $xhr;

	function __construct()
	{
		parent::__construct();

		$this->view = Res::view();
		$this->load = Res::load();
		$this->xhr = Res::Xhr();
	}

	final public function method($resource)
	{
		$page = Helper::getPath($resource['page'], 'method');
		$action = Helper::getPath($resource['action']);
		$params = $resource['params'];
		$default = $resource['default']['method'];
		$methods = array(
			$page . "_" . $action,
			$page . "_" . $default,
			$default . "_" . $action
		);
		foreach ($methods as $method) {
			if (method_exists($this, $method))
				$this->$method($action, $params);
		}
		if (isset($params[3]) and $params[0] === \app\confs\sys\xhr_param__) {
			$this->_xhr($params[0], $params[1], $params[2], $params[3]);
		}
		$this->$default($action, $params);
	}

	private function _xhr($param, $subj, $context, $arg)
	{
		if ($this->xhr->header() !== Res::session()->xid()) {
			return false;
		}
		switch ($subj) {
			case 'post':
			case 'get':
				switch ($arg) {
					case 'json':
					case 'html':
						$this->view->request = $this->xhr->$subj();
						$this->xhr->response($this->view->page("$param/$subj/$context"), $arg);
						break 2;
				}
				break;
			case 'form':
				if ($this->$arg instanceof \sys\modules\Form) {
					$this->view->request = $this->xhr->$context();
					$this->xhr->response($this->$arg->submit($context));
				}
				break;
		}
	}


}
