<?php

namespace sys\components;

use sys\Init;
use sys\components\Compositor;

abstract class Constructor extends Compositor {

	protected $load, $view, $session, $request;

	function __construct()
	{
		parent::__construct();

		$this->load = Init::load();
		$this->view = Init::view();
		$this->session = Init::session();
		$this->request = Init::request();
	}

}
