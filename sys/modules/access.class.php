<?php

namespace sys\modules;

use sys\Init;

class Access {

	public function isAuth()
	{
		return (Init::session()->user('id') and Init::session()->user('token'));
	}

	public function setRule($rule, $role = null, $user_role = null)
	{
		if (!$this->resolve($rule, $role, $user_role)) {
			Init::route()->error(403);
		}
	}

	public function resolve($rule, $role = null, $user_role = null)
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
				$role = (array) $role;
				$user_role = (array) $user_role;
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
