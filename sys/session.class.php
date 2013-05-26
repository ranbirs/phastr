<?php

namespace sys;

class Session {

	private static $sid, $xid, $gid, $key;

	private static function _init()
	{
		self::$xid = \sys\utils\Hash::rand();
		self::$gid = \sys\utils\Hash::rid(true);
		self::$key = self::keygen();

		$_SESSION['_sid'] = self::$sid;
		$_SESSION['_gid'] = self::$gid;
		$_SESSION['_key'] = self::$key;

		$_SESSION[self::$sid]['_xid'] = self::$xid;
		$_SESSION[self::$sid]['_timestamp'][0] = microtime(true);

		$_SESSION[self::$sid]['_client']['ua'] = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : "";
		$_SESSION[self::$sid]['_client']['ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION[self::$sid]['_client']['lang'] = \app\confs\app\lang__;
	}

	public static function start()
	{
		session_start();
		self::$sid = session_id();

		if (!isset($_SESSION['_sid']) or self::$sid !== $_SESSION['_sid']) {
			self::_init();
		}

		self::$xid = $_SESSION[self::$sid]['_xid'];
		self::$gid = $_SESSION['_gid'];
		self::$key = $_SESSION['_key'];

		$_SESSION[self::$sid]['_timestamp'][1] = microtime(true);
	}

	public static function sid()
	{
		return self::$sid;
	}

	public static function xid()
	{
		return self::$xid;
	}

	public static function key()
	{
		return self::$key;
	}

	public static function keygen($key = null)
	{
		$gen = \sys\utils\Hash::get(self::$sid . self::$xid . self::$gid, 'sha1');
		if ($key) {
			return ($key === $gen);
		}
		return $gen;
	}

	public static function timestamp($key = 0, $set = false)
	{
		if ($set) {
			return self::set('_timestamp', $key, microtime(true));
		}
		return self::get('_timestamp', $key);
	}

	public static function uid()
	{
		return self::get('_user', 'uid');
	}

	public static function token()
	{
		return self::get('_user', 'token');
	}

	public static function client($key = 'lang')
	{
		return self::get('_client', $key);
	}

	public static function get($type, $key = null)
	{
		if ($key or $key === 0) {
			if (isset($_SESSION[self::$sid][$type][$key])) {
				return $_SESSION[self::$sid][$type][$key];
			}
			return false;
		}
		if (isset($_SESSION[self::$sid][$type])) {
			return $_SESSION[self::$sid][$type];
		}
		return false;
	}

	public static function set($type, $key, $value = null)
	{
		$value = (!$value and $value !== 0) ? \sys\utils\Hash::rand() : $value;
		$_SESSION[self::$sid][$type][$key] = $value;

		return $value;
	}

	public static function drop($type, $key)
	{
		if (self::get($type, $key)) {
			unset($_SESSION[self::$sid][$type][$key]);

			return true;
		}
		return false;
	}

	public static function quit()
	{
		session_unset();
		session_destroy();
	}

}
