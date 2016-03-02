<?php


namespace PetaKami\Collections;


use Phalcon\Mvc\Micro\Collection;

class PkUserCollection extends Collection
{

    public function __construct()
    {
        $this->setHandler('\PetaKami\Controllers\PkUserController', true);
        $this->setPrefix('/user');

        $this->get('/me', 'me');

        $this->post('/authenticate', 'authenticate');
        $this->post('/register', 'register');
    }
}