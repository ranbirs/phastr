<?php

namespace sys;

class Util {

	private $_helper, $_loader, $_hash, $_html, $_conf, $_vocab;

	public function helper()
	{
		return (isset($this->_helper)) ? $this->_helper : $this->_helper = new \sys\utils\Helper;
	}

	public function loader()
	{
		return (isset($this->_loader)) ? $this->_loader : $this->_loader = new \sys\utils\Loader;
	}

	public function hash()
	{
		return (isset($this->_hash)) ? $this->_hash : $this->_hash = new \sys\utils\Hash;
	}

	public function html()
	{
		return (isset($this->_html)) ? $this->_html : $this->_html = new \sys\utils\Html;
	}

	public function conf()
	{
		return (isset($this->_conf)) ? $this->_conf : $this->_conf = new \sys\utils\Conf;
	}

	public function vocab()
	{
		return (isset($this->_vocab)) ? $this->_vocab : $this->_vocab = new \sys\utils\Vocab;
	}

}
