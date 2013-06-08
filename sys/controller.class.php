<?php

namespace sys;

use sys\Res;
use sys\Compositor;
use sys\utils\Helper;

class Controller extends Compositor {

	protected $load, $view, $xhr;

	function __construct()
	{
		parent::__construct();

		$this->load = Res::load();
		$this->view = Res::view();
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
