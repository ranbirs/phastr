<?php

namespace app\modules;

class Vocab
{

	public function t($const, $context, $args = null, $lang = \app\confs\Config::lang__)
	{
		return $this->constant($const, ($lang) ? $lang . '\\' . $context : $context, $args);
	}

	protected function constant($const, $context, $args = null)
	{
		$format = constant('\\app\\vocabs\\' . $context . '::' . $const);
		return call_user_func_array('sprintf', (array) $format + (array) $args);
	}

}
