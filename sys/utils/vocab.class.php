<?php

namespace sys\utils;

class Vocab extends \sys\Utils {

	public function t($const, $context, $lang = \app\confs\config\lang__)
	{
		if (is_null($constant = $this->getConst($const, $context))) {
			\sys\Init::load()->conf($context, $lang);
			$constant = $this->getConst($const, $context);
		}
		return $constant;
	}

	public function getConst($const, $context)
	{
		return constant('\\app\\vocabs\\' . $context . '\\' . $const);
	}

}
