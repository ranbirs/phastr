<?php

namespace app\navs;

class User_nav extends \sys\modules\Nav {

	function __construct()
	{
		parent::__construct();
	}

	protected function build($data = null)
	{
		if (\sys\Res::session()->token()) {
			$this->item("Sign out", "user/logout");
		}
		else {
			$this->item("Sign in", "user/login");
			$this->item("Sign up", "user/register");
		}
	}

}
