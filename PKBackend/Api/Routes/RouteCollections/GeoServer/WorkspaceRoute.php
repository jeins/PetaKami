<?php

use Phalcon\Mvc\Micro\Collection;

return call_user_func(function(){
    $collection = new Collection();
    $collection->setPrefix('/v1/gs');
    $collection->setHandler('\PetaKami\Controllers\GeoServer\WorkspaceController', true);

    $collection->get('/workspaces', 'getWorkspaces');
    $collection->get('/workspace/{workspace}/draw', 'getWorkspaceWithDrawTyp');

    return $collection;
});
