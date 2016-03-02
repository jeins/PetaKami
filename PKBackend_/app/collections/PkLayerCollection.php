<?php


namespace PetaKami\Collections;


use Phalcon\Mvc\Micro\Collection;

class PkLayerCollection extends Collection
{
    public function __construct()
    {
        $this->setHandler('\PetaKami\Controllers\PkLayerController', true);
        $this->setPrefix('/ulayer');

        $this->post('/', 'addUserLayer');
    }
}