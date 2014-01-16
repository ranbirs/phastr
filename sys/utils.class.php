<?php

namespace sys;

class Utils {

	function __get($name)
	{
		$class = '\\sys\\utils\\' . $name;
		return $this->$name = new $class;
	}

}
