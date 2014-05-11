<?php

namespace sys\handlers;

class Error
{

	public function exception($exception)
	{
		print $exception->getTraceAsString();
	}

	public function error()
	{
		debug_print_backtrace();
	}

}