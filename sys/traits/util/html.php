<?php

namespace sys\traits\util;

trait Html
{

	private $_html_util;

	protected function html()
	{
		return (isset($this->_html_util)) ? $this->_html_util : $this->_html_util = new \sys\utils\Html();
	}

}
