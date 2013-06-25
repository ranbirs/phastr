<?php

namespace sys;

use sys\Res;
use sys\Call;
use sys\Inst;

class Init extends Res {

	public static function start()
	{
		Call::conf('constants');
		Call::vocab('sys');

		self::$resource = self::init();

		if (!self::$resource) {
			Inst::view()->error(404, self::$error);
		}

		Call::conf('autoload');

		if (self::$resource['default']['master']) {
			Call::controller(self::$resource['default']['master']);
		}
		Call::controller(self::$resource['controller'])
			->dispatch(self::$resource['default']['method'], self::$resource['page'], self::$resource['action'], self::$resource['params']);
		exit();
	}

}
