<?php

namespace sys\utils;

class Hash {

	private static $conf = array(
		'algo' => \app\confs\hash\algo__,
		'cost' => \app\confs\hash\cost__,
		'salt' => \app\confs\hash\salt__
	);

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
		return crypt($data, self::$conf['algo'] . self::$conf['cost'] . '$' . self::salt(self::$conf['salt']));
	}

	public static function resolve($hash, $data, $algo = 'sha512', $key = \app\confs\app\hash__)
	{
		if ($algo) {
			$subj = self::get($data, $algo, $key);
		}
		else {
			$salt = substr($hash, 0, strlen(self::$conf['algo'] . self::$conf['cost']) + 1 + self::$conf['salt']);
			$subj = crypt($data, $salt);
		}
		return ($hash === $subj);
	}

}
