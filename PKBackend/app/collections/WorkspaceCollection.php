<?php


namespace PetaKami\Collections;

use Phalcon\Mvc\Micro\Collection;

class WorkspaceCollection extends Collection
{
    /**
     * workspace routes
     */
    public function __construct()
    {
        $this->setHandler('\PetaKami\Controllers\WorkspaceController', true);
        $this->setPrefix('/workspace');

        $this->get('/all', 'all');
        $this->get('/{workspace}/draw', 'findDrawTyp');
    }
}