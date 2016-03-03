<?php


namespace PetaKami\Collections;


use Phalcon\Mvc\Micro\Collection;

class PkLayerCollection extends Collection
{
    public function __construct()
    {
        $this->setHandler('\PetaKami\Controllers\PkLayerController', true);
        $this->setPrefix('/ulayer');

        $this->get('/', 						'getAll');
        $this->get('/user', 		        	'getByUser');
        $this->get('/workspace/{workspace}', 	'getByWorkspace');

        $this->post('/', 						'addUserLayer');
    }
}
