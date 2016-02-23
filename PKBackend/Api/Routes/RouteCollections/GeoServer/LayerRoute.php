<?php

use Phalcon\Mvc\Micro\Collection;

return call_user_func(function(){
    $collection = new Collection();
    $collection->setPrefix('/v1/gs');
    $collection->setHandler('\PetaKami\Controllers\GeoServer\LayerController', true);

    $collection->get('/workspace/{workspace}/layers', 'getLayerByWorkspace');
    $collection->get('/workspace/{workspace}/layer/{layer}/draw', 'getLayersWithDrawType');
    $collection->get('/workspace/{workspace}/layer/{layer}/geojson', 'getLayersInGeoJSON');
    $collection->get('/workspace/{workspace}/layer/{layer}/draw/{draw}/geojson', 'getLayerDrawTypeInGeoJSON');
    $collection->get('/workspace/{workspace}/layer/{layer}/bbox', 'getBBox');
    $collection->get('/drawtype/{workspace}/{layerGroupName}/{layer}', 'getDrawTypeFromLayer');
    $collection->get('/layers/{workspace}/{layerGroupName}', 'getLayerFromWorkspace');

    $collection->post('/layer', 'postAction');
    $collection->post('/upload/{type}/{key}', 'upload');

    $collection->put('/layer/{id}', 'putAction');
    $collection->put('/uploadtogs/{workspace}/{layer}/{key}', 'uploadFileToGeoServer');

    return $collection;
});