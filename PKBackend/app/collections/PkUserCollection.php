<?php


namespace PetaKami\Collections;

use Phalcon\Mvc\Micro\Collection;

class PkUserCollection extends Collection
{

    /**
     * user routes
     */
    public function __construct()
    {
        $this->setHandler('\PetaKami\Controllers\PkUserController', true);
        $this->setPrefix('/user');

        $this->get('/me',                       'me');
        $this->get('/active/{hash}',            'setUserActive');

        $this->post('/authenticate',            'authenticate');
        $this->post('/register',                'register');
    }
}