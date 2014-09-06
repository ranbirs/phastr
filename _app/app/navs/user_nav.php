<?php

namespace app\navs;

class User_nav extends \sys\modules\Nav
{

    protected function items()
    {
        $this->load()->module('access', 'app');

        if ($this->access->isAuth()) {
            $this->item('Sign out', 'user/logout');
        } else {
            $this->item('Sign in', 'user/login');
            $this->item('Sign up', 'user/register');
        }
    }

}
