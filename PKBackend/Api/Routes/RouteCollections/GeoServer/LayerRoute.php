<?php

use Phalcon\Mvc\Micro\Collection;

return call_user_func(function(){
    $collection = new Collection();
    $collection->setPrefix('/v1/gs/layer');
    $collection->setHandler('\PetaKami\Controllers\GeoServer\LayerController', true);

    $collection->get('/', 'get');
    $collection->post('/', 'postAction');
    $collection->put('/{id}', 'putAction');

    return $collection;
});