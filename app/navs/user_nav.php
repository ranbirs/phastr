<?php

namespace app\navs;

class User_nav extends \sys\modules\Nav {

	function __construct()
	{
		parent::__construct();
	}

	protected function build()
	{
		$this->open(array("nav", "ajax-load", "pull-right"));

		if (\sys\Session::token()) {
			$this->item("Sign out", "user/logout");
		}
		else {
			$this->item("Sign in", "user/login");
			$this->item("Sign up", "user/register");
		}

		$this->close();
	}

}
