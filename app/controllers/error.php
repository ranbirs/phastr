<?php

namespace app\controllers;

class Error extends \sys\Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function scope()
	{
		$this->view->layout("error/" . \sys\Res::get('page'));
	}

}
