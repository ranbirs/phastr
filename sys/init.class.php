<?php

namespace sys;

use sys\Res;
use sys\Load;

class Init extends Res {

	private static $load, $view, $session, $xhr;

	function __construct()
	{
		parent::__construct();

		if (isset(self::$error)) {
			self::view()->error(404, self::$error);
		}
		Load::conf('autoload');
		if (self::$defaults['master']) {
			Load::controller(self::$defaults['master']);
		}
		Load::controller(self::controller())
			->dispatch(self::$defaults['method'], self::page(), self::action(), self::params());
		exit();
	}

	public static function load($inst = false)
	{
		if (!isset(self::$load) or $inst)
			self::$load = new \sys\Load();
		return self::$load;
	}

	public static function view($inst = false)
	{
		if (!isset(self::$view) or $inst)
			self::$view = new \sys\View();
		return self::$view;
	}

	public static function session($inst = false)
	{
		if (!isset(self::$session) or $inst)
			self::$session = new \sys\Session();
		return self::$session;
	}

	public static function xhr($inst = false)
	{
		if (!isset(self::$xhr) or $inst)
			self::$xhr = new \sys\modules\Xhr();
		return self::$xhr;
	}

}
