<?php

namespace sys\utils;

use sys\Util;

class Hash extends Util {

	const algo__ = \app\confs\hash\algo__;
	const key__ = \app\confs\hash\key__;
	const cipher__ = \app\confs\hash\cipher__;
	const cost__ = \app\confs\hash\cost__;
	const salt__ = \app\confs\hash\salt__;
	const range__ = \app\confs\hash\range__;

	public function gen($data = '', $algo = self::algo__, $key = self::key__)
	{
		if (empty($data))
			$data = uniqid($this->salt(), true);
		return ($key) ? hash_hmac($algo, $data, $key) : hash($algo, $data);
	}
	
	public function cipher($data = '')
	{
		return crypt($data, self::cipher__ . self::cost__ . '$' . $this->salt(self::salt__));
	}
	
	public function resolve($hash, $data = '', $algo = self::algo__, $key = self::key__)
	{
		if ($algo) {
			$subj = $this->gen($data, $algo, $key);
		}
		else {
			$salt = substr($hash, 0, strlen(self::cipher__ . self::cost__) + 1 + self::salt__);
			$subj = crypt($data, $salt);
		}
		return ($hash === $subj);
	}

	public function rand($length = 0, $algo = 'sha1')
	{
		$length = (int) $length;
		$hash = hash($algo, $this->salt());
		if ($length > 0)
			$hash = ($length > strlen($hash)) ? str_pad($hash, $length, $hash) : substr($hash, 0, $length);
		return $hash;
	}

	public function salt($length = 16, $range = self::range__)
	{
		$length = (int) $length;
		$index_range = strlen($range) - 1;
		$salt = '';
		for ($c = 0; $c < $length; $c++)
			$salt .= $range[mt_rand(0, $index_range)];
		return $salt;
	}

}
