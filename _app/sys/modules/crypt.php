<?php

namespace sys\modules;

use app\confs\Crypt as __crypt;

class Crypt
{
	
	public function iv($cipher = __crypt::cipher__, $mode = __crypt::mode__, $source = __crypt::source__)
	{
		return mcrypt_create_iv(mcrypt_get_iv_size($cipher, $mode), $source);
	}
	
	public function encrypt($data = null, $key, $iv, $cipher = __crypt::cipher__, $mode = __crypt::mode__)
	{
		$data = base64_encode(json_encode($data));
		return mcrypt_encrypt($cipher, $key, $data, $mode, $iv);
	}
	
	public function decrypt($data = null, $key, $iv, $cipher = __crypt::cipher__, $mode = __crypt::mode__)
	{
		$data = mcrypt_decrypt($cipher, $key, $data, $mode, $iv);
		return json_decode(base64_decode(trim($data)));
	}

}