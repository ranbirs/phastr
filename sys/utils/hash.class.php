<?php

namespace sys\utils;

class Hash {

	private static $cipher = \app\confs\hash\cipher__;
	private static $cost = \app\confs\hash\cost__;
	private static $salt = \app\confs\hash\salt__;

	public static function id($id = "", $algo = 'sha1')
	{
		if (!$id)
			$id = uniqid(mt_rand(), true);
		return self::get($id, $algo, false);
	}

	public static function rand($length = 0, $algo = 'sha1')
	{
		$hash = hash($algo, self::salt());
		$length = (int) $length;
		if ($length > 0)
			$hash = ($length > strlen($hash)) ? str_pad($hash, $length, $hash) : substr($hash, 0, $length);
		return str_shuffle($hash);
	}

	public static function salt($length = 16, $range = null)
	{
		$range = (empty($range)) ?
			array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9)) :
			((is_array($range)) ? array_values($range) : explode($range));
		$scope = count($range) - 1;
		$length = (int) $length;
		$salt = [];

		for ($c = 0; $c < $length; $c++) {
			$rand = mt_rand(0, $scope);
			$salt[] = $range[$rand];
		}
		return implode($salt);
	}

	public static function get($data = "", $algo = \app\confs\hash\algo__, $key = \app\confs\hash\key__)
	{
		return ($key) ? hash_hmac($algo, $data, $key) : hash($algo, $data);
	}

	public static function cipher($data = "")
	{
		return crypt($data, self::$cipher . self::$cost . '$' . self::salt(self::$salt));
	}

	public static function resolve($hash, $data = "", $algo = \app\confs\hash\algo__, $key = \app\confs\hash\key__)
	{
		if ($algo) {
			$subj = self::get($data, $algo, $key);
		}
		else {
			$salt = substr($hash, 0, strlen(self::$cipher . self::$cost) + 1 + self::$salt);
			$subj = crypt($data, $salt);
		}
		return ($hash === $subj);
	}

}
