<?php

namespace sys;

class Session {

	private static $sid, $xid, $gid, $key;

	private static $timestamp = array();

	private static function _init()
	{
		self::$timestamp[0] = microtime(true);
		self::$xid = \sys\utils\Hash::rand();
		self::$gid = \sys\utils\Hash::rid(true);
		self::$key = self::keygen();

		$_SESSION['_sid'] = self::$sid;
		$_SESSION['_gid'] = self::$gid;
		$_SESSION['_key'] = self::$key;
		$_SESSION['_timestamp'][0] = self::$timestamp[0];

		$_SESSION[self::$sid]['_xid'] = self::$xid;
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

		self::$timestamp[1] = microtime(true);
		self::$xid = $_SESSION[self::$sid]['_xid'];
		self::$gid = $_SESSION['_gid'];
		self::$key = $_SESSION['_key'];
		$_SESSION['_timestamp'][1] = self::$timestamp[1];
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

	public static function timestamp($start = true)
	{
		$key = ($start) ? 0 : 1;

		return self::$timestamp[$key];
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
		if ($key) {
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
		$hash = (!$value and $value !== 0) ? \sys\utils\Hash::rand() : $value;
		$_SESSION[self::$sid][$type][$key] = $hash;

		return $hash;
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
