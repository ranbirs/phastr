<?php

namespace sys;

use sys\Init;
use sys\modules\Xhr;
use sys\utils\Helper;

class Res extends Init {

	public static function view()
	{
		return self::$view;
	}

	public static function load()
	{
		return self::$load;
	}

	public static function session()
	{
		return self::$session;
	}

	public static function xhr()
	{
		if (!isset(self::$xhr))
			self::$xhr = new Xhr();
		return self::$xhr;
	}

	public static function get($key = null, $arg = null)
	{
		switch ($key) {
			case null:
				$resource = self::$resource;
				break;
			case 'params':
				$resource = (is_numeric($arg)) ?
					((isset(self::$resource[$key][$arg])) ? self::$resource[$key][$arg] : null) :
					self::$resource[$key];
				break;
			case 'args':
				if (!isset(self::$resource[$key]))
					self::$resource[$key] = Helper::getArgs(self::$resource['params']);
				$resource = ($arg) ?
					((isset(self::$resource[$key][$arg])) ? self::$resource[$key][$arg] : null) :
					self::$resource[$key];
				break;
			default:
				$resource = ($arg) ?
					((isset(self::$resource[$key][$arg])) ? self::$resource[$key][$arg] : null) :
					self::$resource[$key];
		}
		return $resource;
	}

}
