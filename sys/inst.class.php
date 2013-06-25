<?php

namespace sys;

class Inst {

	protected static $load, $view, $session, $xhr;

	public static function load()
	{
		if (!isset(self::$load))
			self::$load = new \sys\components\Load();
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
