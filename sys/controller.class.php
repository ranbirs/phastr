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
			$default . "_" . $action,
			$default
		);
		foreach ($methods as $method) {
			if (method_exists($this, $method))
				$this->$method($action, $params);
		}
	}

}
