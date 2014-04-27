<?php

namespace sys\handlers;

class Error
{
	
	public function exception($exception)
	{
		return $this->output($exception->getTrace());
	}
	
	public function error()
	{
		return $this->output(debug_backtrace());
	}
	
	public function shutdown()
	{
		return $this->output(error_get_last());
	}
	
	protected function output($msg = null)
	{
		print '<pre>' . print_r($msg, true) . '</pre>';

		//exit();
	}
	
}