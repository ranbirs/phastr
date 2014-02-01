<?php

namespace sys\utils;

use sys\Util;

class Hash extends Util
{

	public function gen($data = '', $algo = \app\confs\Hash::algo__, $key = \app\confs\Hash::key__)
	{
		if (empty($data)) {
			$data = uniqid($this->salt(), true);
		}
		return ($key) ? hash_hmac($algo, $data, $key) : hash($algo, $data);
	}

	public function cipher($data = '')
	{
		return crypt($data, 
			\app\confs\Hash::cipher__ . \app\confs\Hash::cost__ . '$' . $this->salt(\app\confs\Hash::salt__));
	}

	public function resolve($hash, $data = '', $algo = \app\confs\Hash::algo__, $key = \app\confs\Hash::key__)
	{
		if ($algo) {
			$subj = $this->gen($data, $algo, $key);
		}
		else {
			$salt = substr($hash, 0, 
				strlen(\app\confs\Hash::cipher__ . \app\confs\Hash::cost__) + 1 + \app\confs\Hash::salt__);
			$subj = crypt($data, $salt);
		}
		return ($hash === $subj);
	}

	public function rand($length = 0, $algo = 'sha1')
	{
		$length = (int) $length;
		$hash = hash($algo, $this->salt());
		if ($length > 0) {
			$hash = ($length > strlen($hash)) ? str_pad($hash, $length, $hash) : substr($hash, 0, $length);
		}
		return $hash;
	}

	public function salt($length = 16, $range = \app\confs\Hash::range__)
	{
		$length = (int) $length;
		$index_range = strlen($range) - 1;
		$salt = '';
		for ($c = 0; $c < $length; $c ++)
			$salt .= $range[mt_rand(0, $index_range)];
		return $salt;
	}

}
