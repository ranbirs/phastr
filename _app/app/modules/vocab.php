<?php

namespace app\modules;

class Vocab
{

    public function t($const, $context, $args = null, $lang = \sys\configs\Session::lang__)
    {
        return $this->constant($const, ($lang) ? $lang . '\\' . $context : $context, $args);
    }

    protected function constant($const, $context, $args = null)
    {
        $format = constant('\\' . app__ . '\\vocabs\\' . $context . '::' . $const);
        return call_user_func_array('sprintf', (array)$format + (array)$args);
    }

}
