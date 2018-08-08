<?php

namespace sys\utils;

class Html
{

	public static function attr($attr = [])
	{
		return ($attr = Helper::attr($attr)) ? ' ' . implode(' ', Helper::iterate_join($attr, '="', '', '"')) : '';
	}

}