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

		$this->_method();
	}

	private function _method()
	{
		$res = Res::get();
		$master = $res['master'];

		if (Helper::getClassName(get_class($this)) === $master) {
			return false;
		}
		$this->init();

		$default = $res['method'];
		$page = Helper::getPath($res['page'], 'method');
		$action = Helper::getPath($res['action']);
		$params = $res['params'];
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
