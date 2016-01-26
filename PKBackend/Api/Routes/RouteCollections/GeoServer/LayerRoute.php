<?php

use Phalcon\Mvc\Micro\Collection;

return call_user_func(function(){
    $collection = new Collection();
    $collection->setPrefix('/v1/gs');
    $collection->setHandler('\PetaKami\Controllers\GeoServer\LayerController', true);

    $collection->get('/layer', 'get');
    $collection->post('/layer', 'postAction');
    $collection->put('/layer/{id}', 'putAction');

    return $collection;
});