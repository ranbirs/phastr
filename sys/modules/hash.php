<?php

namespace sys\modules;

use app\confs\Hash as __hash;

class Hash
{

	public function gen($data = '', $algo = __hash::algo__, $key = __hash::key__)
	{
		if (empty($data)) {
			$data = uniqid($this->salt(16), true);
		}
		return ($key) ? hash_hmac($algo, $data, $key) : hash($algo, $data);
	}

	public function cipher($data = '')
	{
		return crypt($data, __hash::cipher__ . __hash::cost__ . '$' . $this->salt(__hash::salt__));
	}

	public function resolve($hash, $data = '', $algo = __hash::algo__, $key = __hash::key__)
	{
		if ($algo) {
			$subj = $this->gen($data, $algo, $key);
		}
		else {
			$salt = substr($hash, 0, strlen(__hash::cipher__ . __hash::cost__) + 1 + __hash::salt__);
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

	public function salt($length = 16, $chars = __hash::chars__)
	{
		$length = (int) $length;
		$limit = strlen($chars) - 1;
		$salt = '';

		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $limit);
			$salt .= $chars[$rand];
		}
		return $salt;
	}

}
