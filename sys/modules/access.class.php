<?php

namespace sys\modules;

use sys\Init;

class Access {

	public function isAuth()
	{
		return (Init::session()->user('id') and Init::session()->user('token'));
	}

	public function setRule($rule, $perm = null, $role = null)
	{
		if (!$this->resolve($rule, $perm, $role)) {
			Init::route()->error(403);
		}
	}

	public function resolve($rule, $perm = null, $role = null)
	{
		switch ($rule) {
			case 'public':
				return ($this->isAuth() === false);
			case 'private':
				return ($this->isAuth() === true);
			case 'role':
				if (is_null($perm) or is_null($role) or $this->isAuth() === false) {
					return false;
				}
				$role = array_intersect((array) $perm, (array) $role);
				if (!empty($role)) {
					return $role;
				}
				return false;
			default:
				return false;
		}
	}

}
