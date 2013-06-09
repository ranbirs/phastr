<?php

namespace sys\utils;

class Hash {

	private static $algo = \app\confs\hash\algo__;
	private static $cost = \app\confs\hash\cost__;
	private static $salt = \app\confs\hash\salt__;

	public static function rid($entropy = false)
	{
		return uniqid(mt_rand(), $entropy);
	}

	public static function rand($size = 0, $algo = 'sha1')
	{
		$hash = hash($algo, self::rid());
		$size = (int) $size;
		if ($size > 0)
			$hash = ($size > strlen($hash)) ? str_pad($hash, $size, $hash) : substr($hash, 0, $size);
		return str_shuffle($hash);
	}

	public static function salt($size = 16)
	{
		$chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
		$scope = count($chars) - 1;
		$size = (int) $size;
		$salt = array();

		for ($c = 0; $c < $size; $c++) {
			$rand = mt_rand(0, $scope);
			$salt[] = $chars[$rand];
		}
		return implode($salt);
	}

	public static function get($data, $algo = 'sha512', $key = \app\confs\sys\hash__)
	{
		return ($key) ? hash_hmac($algo, $data, $key) : hash($algo, $data);
	}

	public static function gen($data)
	{
		return crypt($data, self::$algo . self::$cost . '$' . self::salt(self::$salt));
	}

	public static function resolve($hash, $data, $algo = 'sha512', $key = \app\confs\sys\hash__)
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
