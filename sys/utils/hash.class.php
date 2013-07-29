<?php

namespace sys\utils;

class Hash {

	private static $algo = \app\confs\hash\algo__;
	private static $cost = \app\confs\hash\cost__;
	private static $salt = \app\confs\hash\salt__;

	public static function rid($prefix = "", $algo = 'sha1')
	{
		if (!$prefix)
			$prefix = mt_rand();
		return self::get(uniqid($prefix, true), $algo, null);
	}

	public static function rand($length = 0, $algo = 'sha1')
	{
		$hash = hash($algo, self::rid());
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
		$salt = array();

		for ($c = 0; $c < $length; $c++) {
			$rand = mt_rand(0, $scope);
			$salt[] = $range[$rand];
		}
		return implode($salt);
	}

	public static function get($data, $algo = 'sha512', $key = \app\confs\config\hash__)
	{
		return ($key) ? hash_hmac($algo, $data, $key) : hash($algo, $data);
	}

	public static function gen($data)
	{
		return crypt($data, self::$algo . self::$cost . '$' . self::salt(self::$salt));
	}

	public static function resolve($hash, $data, $algo = 'sha512', $key = \app\confs\config\hash__)
	{
		if ($algo) {
			$subj = self::get($data, $algo, $key);
		}
		else {
			$salt = substr($hash, 0, strlen(self::$algo . self::$cost) + 1 + self::$salt);
			$subj = crypt($data, $salt);
		}
		return ($hash === $subj);
	}

}
