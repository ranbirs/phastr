<?php

namespace sys;

use sys\Init;

use sys\modules\Xhr;

use sys\utils\Helper;

class Res extends Init {

	public static function load()
	{
		return self::$load;
	}

	public static function view()
	{
		return self::$view;
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
				return self::$resource;
			break;
			default:
				if (!isset(self::$resource[$key])) {
					return false;
				}
			break;
			case 'params':
				if (is_numeric($arg)) {
					if (isset(self::$resource[$key][$arg])) {
						return self::$resource[$key][$arg];
					}
					return false;
				}
			break;
			case 'args':
				if (!isset(self::$resource[$key])) {
					self::$resource[$key] = Helper::getArgs(self::$resource['params']);
				}
				if ($arg) {
					if (isset(self::$resource[$key][$arg])) {
						return self::$resource[$key][$arg];
					}
					return false;
				}
			break;
		}
		return self::$resource[$key];
	}

}
