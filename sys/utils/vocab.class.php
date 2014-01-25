<?php

namespace sys\utils;

use sys\Util;

class Vocab extends Util {

	public function t($const, $context, $lang = \app\confs\config\lang__)
	{
		if (is_null($constant = $this->constant($const, $context))) {
			$this->loader()->resolveInclude(($lang) ? $lang . '/' . $context : $context, 'vocab', false);//
			$constant = $this->constant($const, $context);
		}
		return $constant;
	}

	protected function constant($const, $context)
	{
		return constant('\\app\\vocabs\\' . $context . '\\' . $const);
	}

}
