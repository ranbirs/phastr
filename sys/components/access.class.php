<?php

namespace sys\components;

use sys\Res;

class Access {

	public static function resolveRule($rule, $role)
	{
		if (is_array($role)) {
			return false;
		}
		switch ($role) {
			case 'public':
				switch ($rule) {
					case 'deny':
						$access = (!is_null(Res::session()->token()));
						break 2;
				}
				$access = true;
				break;
			case 'private':
				switch ($rule) {
					case 'deny':
						$access = (is_null(Res::session()->token()));
						break 2;
				}
				$access = true;
				break;
			case 'role':
			default:
				$access = false;
		}
		return $access;
	}

}