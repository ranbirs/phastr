<?php

namespace sys\handlers;

class Error
{

	public function exception($ex)
	{
		return $this->output('Exception', $ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
	}

	public function error($errno, $errstr, $errfile, $errline, $errcontext)
	{
		return $this->output('Error', $errno, $errstr, $errfile, $errline);
	}
	
	public function output($handler, $code = null, $message = null, $file = null, $line = null)
	{
		print '<pre style="white-space: pre-line;">';
		print '<strong>' . $handler . '</strong> ';
		print '<code>[' . $code . ']</code> ';
		print '<em>' . $message . '</em> ';
		print '<strong>' . $file . ':' . $line . '</strong>';
		print '</pre>';	
	}

}