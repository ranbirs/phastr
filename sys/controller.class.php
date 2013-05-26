<?php

namespace sys;

class Controller {

	protected $view, $load, $xhr;

	private static $instance;

	function __construct()
	{
		$this->view = \sys\Res::view();
		$this->load = \sys\Res::load();
		$this->xhr = \sys\Res::Xhr();

		self::$instance = &$this;

		$this->_method();
	}

	public static function instance()
	{
		return self::$instance;
	}

	private function _master($master)
	{
		$controller = \sys\utils\Helper::getClassName(get_class($this));

		return ($controller === $master);
	}

	private function _method()
	{
		$res = \sys\Res::get();

		if ($res['master']) {
			if ($this->_master($res['master'])) {
				return false;
			}
		}

		$this->init();

		$default = $res['method'];
		$page = \sys\utils\Helper::getPath($res['page'], 'method');
		$action = \sys\utils\Helper::getPath($res['action']);
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
