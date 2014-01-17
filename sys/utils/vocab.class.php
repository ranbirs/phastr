<?php

namespace sys\utils;

use sys\Util;

class Vocab extends Util {

	public function t($const, $context, $lang = \app\confs\config\lang__)
	{
		if (is_null($constant = $this->getConst($const, $context))) {
			$this->loader->resolveInclude(($lang) ? $lang . '/' . $context : $context, 'vocab', false);//
			$constant = $this->getConst($const, $context);
		}
		return $constant;
	}

	public function getConst($const, $context)
	{
		return constant('\\app\\vocabs\\' . $context . '\\' . $const);
	}

}
