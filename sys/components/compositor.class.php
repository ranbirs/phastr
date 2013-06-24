<?php

namespace sys\components;

use sys\Res;

class Compositor {

	protected $view, $load, $session, $xhr;

	private static $instance;

	function __construct()
	{
		$this->view = Res::view();
		$this->load = Res::load();
		$this->session = Res::session();
		$this->xhr = Res::xhr();

		self::$instance = &$this;
	}

	final public static function instance()
	{
		return self::$instance;
	}

}
