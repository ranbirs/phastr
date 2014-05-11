<?php

namespace sys\modules;

use app\confs\Config as __Config;

class Vocab
{

	public function t($const, $context, $lang = __Config::lang__)
	{
		return $this->constant($const, ($lang) ? $lang . '\\' . $context : $context);
	}

	protected function constant($const, $context)
	{
		return constant('\\app\\vocabs\\' . $context . '::' . $const);
	}

}
