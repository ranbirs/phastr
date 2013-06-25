<?php

namespace sys\components;

use sys\Inst;
use sys\components\Compositor;

class Constructor extends Compositor {

	protected $load, $view, $session, $xhr;

	function __construct()
	{
		parent::__construct();

		$this->load = Inst::load();
		$this->view = Inst::view();
		$this->session = Inst::session();
		$this->xhr = Inst::xhr();
	}

}
