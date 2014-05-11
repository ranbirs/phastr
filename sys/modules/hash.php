<?php

namespace sys\modules;

use app\confs\Hash as __Hash;

class Hash
{

	public function gen($data = '', $algo = __Hash::algo__, $key = __Hash::key__)
	{
		if (empty($data)) {
			$data = uniqid($this->salt(), true);
		}
		return ($key) ? hash_hmac($algo, $data, $key) : hash($algo, $data);
	}

	public function cipher($data = '')
	{
		return crypt($data, __Hash::cipher__ . __Hash::cost__ . '$' . $this->salt(__Hash::salt__));
	}

	public function resolve($hash, $data = '', $algo = __Hash::algo__, $key = __Hash::key__)
	{
		if ($algo) {
			$subj = $this->gen($data, $algo, $key);
		}
		else {
			$salt = substr($hash, 0, strlen(__Hash::cipher__ . __Hash::cost__) + 1 + __Hash::salt__);
			$subj = crypt($data, $salt);
		}
		return ($hash === $subj);
	}

	public function rand($algo = 'md5', $length = 0)
	{
		$length = (int) $length;
		$hash = hash($algo, $this->salt());
		if ($length > 0) {
			$hash = ($length > strlen($hash)) ? str_pad($hash, $length, $hash) : substr($hash, 0, $length);
		}
		return $hash;
	}

	public function salt($length = 16, $range = __Hash::range__)
	{
		$length = (int) $length;
		$index_range = strlen($range) - 1;
		$salt = '';
		for ($c = 0; $c < $length; $c++)
			$salt .= $range[mt_rand(0, $index_range)];
		return $salt;
	}

}
