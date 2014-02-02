<?php

namespace sys\utils;

class Vocab extends \sys\Util
{

	public function t($const, $context, $lang = \app\confs\Config::lang__)
	{
		return $this->constant($const, ($lang) ? $lang . '\\' . $context : $context);
	}

	protected function constant($const, $context)
	{
		return constant('\\app\\vocabs\\' . $context . '::' . $const);
	}

}
