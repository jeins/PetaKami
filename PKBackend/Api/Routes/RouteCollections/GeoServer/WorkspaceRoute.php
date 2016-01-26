<?php

use Phalcon\Mvc\Micro\Collection;

return call_user_func(function(){
    $collection = new Collection();
    $collection->setPrefix('/v1/gs/workspace');
    $collection->setHandler('\PetaKami\Controllers\GeoServer\WorkspaceController', true);

    $collection->get('/', 'getWorkspaces');
    $collection->get('/{workspace}/draw', 'getWorkspaceWithDrawTyp');

    return $collection;
});
