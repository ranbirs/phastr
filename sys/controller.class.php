<?php

namespace sys;

use sys\Res;

use sys\utils\Helper;

class Controller {

	protected $view, $load, $xhr;

	private static $instance;

	function __construct()
	{
		$this->view = Res::view();
		$this->load = Res::load();
		$this->xhr = Res::Xhr();

		self::$instance = &$this;

		$this->_method();
	}

	public static function instance()
	{
		return self::$instance;
	}

	private function _master($master)
	{
		$controller = Helper::getClassName(get_class($this));

		return ($controller === $master);
	}

	private function _method()
	{
		$res = Res::get();

		if ($res['master']) {
			if ($this->_master($res['master'])) {
				return false;
			}
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

		return true;
	}

}
