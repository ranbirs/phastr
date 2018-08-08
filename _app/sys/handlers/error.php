<?php

namespace sys\handlers;

class Error
{

	public static function exception($ex)
	{
		print self::output($ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
	}

	public static function error($errno, $errstr, $errfile, $errline, $errcontext)
	{
		print self::output($errno, $errstr, $errfile, $errline);
	}

	private static function output($code = null, $message = null, $file = null, $line = null)
	{
		return "<pre style=\"white-space: pre-line;\">
				<strong>{$code}</strong>&nbsp;<em>{$message}</em>
				<strong>{$file}:{$line}</strong>
			</pre>";
	}

}