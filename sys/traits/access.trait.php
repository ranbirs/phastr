<?php

namespace sys\traits;

use sys\Init;

trait Access {

	protected function isAuth()
	{
		return (Init::session()->user('id') and Init::session()->user('token'));
	}

	protected function setAccess($rule, $role = null, $user_role = null)
	{
		if (!$this->getAccess($rule, $role, $user_role)) {
			Init::view()->error(403);
		}
	}

	protected function getAccess($rule, $role = null, $user_role = null)
	{
		switch ($rule) {
			case 'public':
				return ($this->isAuth() === false);
			case 'private':
				return ($this->isAuth() === true);
			case 'role':
				if (is_null($role) or is_null($user_role) or $this->isAuth() === false) {
					return false;
				}
				if (!is_array($role))
					$role = [$role];
				if (!is_array($user_role))
					$user_role = [$user_role];
				$perm_role = array_intersect($role, $user_role);
				if (!empty($perm_role)) {
					return $perm_role;
				}
				return false;
			default:
				return false;
		}
	}

}
