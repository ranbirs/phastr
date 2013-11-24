<?php

namespace sys\traits;

trait Rest {

	private static $rest;

	public function rest($new = false)
	{
		if (!isset(self::$rest) or $new)
			self::$rest = new \sys\modules\Rest;
		return self::$rest;
	}

}
