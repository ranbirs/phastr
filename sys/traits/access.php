<?php

namespace sys\traits;

use sys\Init;

trait Access {

	protected function permission($rule, $role)
	{
		if (!$this->resolvePermission($rule, $role)) {
			Init::view()->error(403);
		}
	}

	protected function resolvePermission($rule, $role)
	{
		if (is_array($role)) {
			return false;
		}
		switch ($role) {
			case 'public':
				switch ($rule) {
					case 'deny':
						$access = (!is_null(Init::session()->token()));
						break 2;
				}
				$access = false;
				break;
			case 'private':
				switch ($rule) {
					case 'deny':
						$access = (is_null(Init::session()->token()));
						break 2;
				}
				$access = false;
				break;
			case 'role':
			default:
				return false;
		}
		return $access;
	}

}
