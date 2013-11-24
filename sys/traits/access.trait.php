<?php

namespace sys\traits;

trait Access {

	private static $access;

	public function access()
	{
		if (!isset(self::$access))
			self::$access = new \sys\modules\Access();
		return self::$access;
	}

}
