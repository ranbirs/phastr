<?php

namespace app\modules;

class AES
{

	const cipher__ = MCRYPT_RIJNDAEL_256;

	const mode__ = MCRYPT_MODE_CBC;

	const rand__ = MCRYPT_DEV_URANDOM;

	public function iv($cipher = self::cipher__, $mode = self::mode__, $rand = self::rand__)
	{
		return base64_encode(mcrypt_create_iv(mcrypt_get_iv_size($cipher, $mode), $rand));
	}

	public function encrypt($data = null, $key, $iv)
	{
		$data = base64_encode(json_encode($data));
		return base64_encode(mcrypt_encrypt(self::cipher__, $key, $data, self::mode__, base64_decode($iv)));
	}

	public function decrypt($data = null, $key, $iv)
	{
		$data = mcrypt_decrypt(self::cipher__, $key, base64_decode($data), self::mode__, base64_decode($iv));
		return json_decode(base64_decode(trim($data)));
	}

}