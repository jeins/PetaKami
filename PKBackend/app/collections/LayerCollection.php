<?php


namespace PetaKami\Collections;


use Phalcon\Mvc\Micro\Collection;

class LayerCollection extends Collection
{
    public function __construct()
    {
        $this->setHandler('\PetaKami\Controllers\LayerController', true);
        $this->setPrefix('/layer');

        $this->get('/{workspace}',                                      'getLayersFromWorkspace');
        $this->get('/{workspace}/{layerGroupName}',                     'getLayerAndDrawType');
        $this->get('/{workspace}/{layerGroupName}/geojson',             'getFeatureCollectionGeoJson');
        $this->get('/{workspace}/{layers}/bylayer/geojson',             'getFeatureCollectionFilterByLayer');
        $this->get('/{workspace}/{layerGroupName}/bbox',                'getBbox');
        $this->get('/{workspace}/{layerGroupName}/{layer}/drawtype',    'getDrawType');

        $this->put('/edit',                                             'editLayer');

        $this->post('/geoserver',                                       'geoserver');
        $this->post('/add',                                             'postLayer');
        $this->post('/upload_files/{type}/{key}',                       'postUploadFiles');
        $this->post('/upload_layers/{workspace}/{dataStore}/{key}',     'postUploadLayers');
    }
}