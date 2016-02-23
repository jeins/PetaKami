<?php


namespace PetaKami\Collections;


use Phalcon\Mvc\Micro\Collection;

class UserCollection extends Collection
{

    public function __construct()
    {
        $this->setHandler('\PetaKami\Controllers\UserController', true);
        $this->setPrefix('/user');

        $this->get('/me', 'me');

        $this->post('/authenticate', 'authenticate');
    }
}