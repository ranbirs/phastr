<?php

namespace sys\utils;

class Hash {

	private static $algo = \app\confs\hash\algo__;
	private static $cost = \app\confs\hash\cost__;
	private static $salt = \app\confs\hash\salt__;

	public static function rid($entropy = false)
	{
		return uniqid(mt_rand(), (bool) $entropy);
	}

	public static function rand($length = 0, $algo = 'sha1')
	{
		$hash = hash($algo, self::rid());
		$length = (int) $length;
		if ($length > 0)
			$hash = ($length > strlen($hash)) ? str_pad($hash, $length, $hash) : substr($hash, 0, $length);
		return str_shuffle($hash);
	}

	public static function salt($length = 16)
	{
		$chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
		$scope = count($chars) - 1;
		$length = (int) $length;
		$salt = array();

		for ($c = 0; $c < $length; $c++) {
			$rand = mt_rand(0, $scope);
			$salt[] = $chars[$rand];
		}
		return implode($salt);
	}

	public static function get($data, $algo = 'sha512', $key = \app\confs\app\hash__)
	{
		return ($key) ? hash_hmac($algo, $data, $key) : hash($algo, $data);
	}

	public static function gen($data)
	{
		return crypt($data, self::$algo . self::$cost . '$' . self::salt(self::$salt));
	}

	public static function resolve($hash, $data, $algo = 'sha512', $key = \app\confs\app\hash__)
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
