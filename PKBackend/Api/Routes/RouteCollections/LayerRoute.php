<?php

use Phalcon\Mvc\Micro\Collection;

return call_user_func(function(){
    $layerCollection = new Collection();
    $layerCollection->setPrefix('/v1/layer');
    $layerCollection->setHandler('\PetaKami\Controllers\GeoServer\LayerController', true);

    $layerCollection->get('/', 'get');
    $layerCollection->post('/', 'postAction');
    $layerCollection->put('/{id}', 'putAction');
    return $layerCollection;
});