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
						return (Init::session()->user('id') and Init::session()->user('token'));
				}
				break;
			case 'private':
				switch ($rule) {
					case 'deny':
						return (!Init::session()->user('id') or !Init::session()->user('token'));
				}
				break;
			case 'role':
			default:
				return false;
		}
	}

}
