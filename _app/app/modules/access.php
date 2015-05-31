<?php

namespace app\modules;

class Access
{

    use \sys\Loader;

    function __construct()
    {
        $this->load()->load('sys/modules/session');
        $this->load()->load('app/models/user');
    }

    public function isAuth()
    {
        return ($this->session->keygen($this->session->key()) &&
            $this->session->token() === $this->user->token($this->session->get(['user' => 'id'])));
    }

    public function permission($rule, $perm = null, $role = null)
    {
        if (!$this->resolve($rule, $perm, $role)) {
            $this->load()->init('sys/route')->error(403, 'app/views/layouts/error/403');
        }
    }

    public function resolve($rule, $perm = null, $role = null)
    {
        switch ($rule) {
            case 'public':
                return ($this->isAuth() === false);
            case 'private':
                return ($this->isAuth() === true);
            case 'role':
                if (!$perm || !$role || $this->isAuth() === false) {
                    return false;
                }
                $perm = array_intersect((array)$perm, (array)$role);
                if ($perm) {
                    return $perm;
                }
                return false;
            default:
                return false;
        }
    }

}
