<?php

namespace app\controllers;

class User extends _master
{

    public function init()
    {
        parent::init();

        $this->view->title = 'User';
    }

    public function index()
    {
        $this->access->permission('private');
    }

    public function edit()
    {
        $this->access->permission('private');
    }

    public function login()
    {
        $this->access->permission('public');
        $this->load()->model('user');

        $this->load()->form('user/login_form');
        $this->login_form->user_model = $this->user;

        $this->login_form->get(['title' => 'Authentication form', 'attr' => ['class' => 'form form-horizontal']]);
        $this->view->login_form = $this->login_form->render('bootstrap');
    }

    public function register()
    {
        $this->access->permission('public');
        $this->load()->model('user');

        $this->load()->form('user/register_form');
        $this->register_form->user_model = $this->user;

        $this->view->title = 'New User Registration';
        $this->register_form->get(['title' => 'Registration form', 'attr' => ['class' => 'form form-horizontal']]);
        $this->view->body = $this->register_form->render('bootstrap');
    }

    public function register_verify()
    {
        $this->access->permission('public');
        $this->load()->model('user');

        $token = $this->route->params(1);
        if ($this->route->params(0) !== $this->session->key()) {
            $this->route->error(404);
        }
        if ($token) {
            if ($this->user->verify($token)) {
                $this->view->title = 'New User Verification';
                $this->view->body = $this->view->page('register/verify');
                return true;
            }
        }
        $this->route->error(404);
    }

    public function logout()
    {
        $this->access->permission('private');
        $this->session->drop('user');
    }

}
