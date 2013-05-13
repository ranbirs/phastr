<?php

namespace sys;

class Common {

	protected $view, $load, $xhr;

	protected $_sid, $_xid, $_key;

	function __construct()
	{
		$this->view = \sys\Init::view();
		$this->load = \sys\Init::load();
		$this->xhr = \sys\Init::xhr();

		$this->_sid = \sys\Session::sid();
		$this->_xid = \sys\Session::xid();
		$this->_key = \sys\Session::key();
	}

}
