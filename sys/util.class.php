<?php

namespace sys;

class Util {

	function __get($name)
	{
		$class = '\\sys\\utils\\' . $name;
		return $this->$name = new $class;
	}

}
