<?php

namespace sys;

use sys\Res;
use sys\Load;

class Init extends Res {

	private static $load, $view, $session, $xhr;

	public static function start()
	{
		Load::conf('constants');
		Load::vocab('sys');

		self::init();

		if (isset(self::$error)) {
			self::view()->error(404, self::$error);
		}

		Load::conf('autoload');

		if (self::$defaults['master']) {
			Load::controller(self::$defaults['master']);
		}
		Load::controller(self::$controller)
			->dispatch(self::$defaults['method'], self::$page, self::$action, self::$params);
		exit();
	}

	public static function load()
	{
		if (!isset(self::$load))
			self::$load = new \sys\Load();
		return self::$load;
	}

	public static function view()
	{
		if (!isset(self::$view))
			self::$view = new \sys\View();
		return self::$view;
	}

	public static function session()
	{
		if (!isset(self::$session))
			self::$session = new \sys\Session();
		return self::$session;
	}

	public static function xhr()
	{
		if (!isset(self::$xhr))
			self::$xhr = new \sys\modules\Xhr();
		return self::$xhr;
	}

}
