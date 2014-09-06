<?php

namespace sys\handlers;

class Error
{

    public function exception($ex)
    {
        print $this->output('Exception', $ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
    }

    public function error($errno, $errstr, $errfile, $errline, $errcontext)
    {
        print $this->output('Error', $errno, $errstr, $errfile, $errline);
    }

    public function output($handler, $code = null, $message = null, $file = null, $line = null)
    {
        return "<pre style=\"white-space: pre-line;\">
			<strong>{$handler}</strong>
			<em>{$message}</em>
			<strong>{$file}:{$line}</strong>
		</pre>";
    }

}