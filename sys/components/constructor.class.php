<?php

namespace sys\components;

use sys\Init;
use sys\components\Compositor;

class Constructor extends Compositor {

	protected $load, $view, $session, $xhr;

	function __construct()
	{
		parent::__construct();

		$this->load = Init::load();
		$this->view = Init::view();
		$this->session = Init::session();
		$this->xhr = Init::xhr();
	}

}
