<?php

namespace sys\utils;

class Html
{

    public static function attr($attr = [])
    {
        $attr = Helper::attr($attr);
        return ($attr) ? ' ' . implode(' ', Helper::iterate_join($attr, '="', '', '"')) : '';
    }

}