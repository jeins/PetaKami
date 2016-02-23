<?php


namespace PetaKami\Collections;


use Phalcon\Mvc\Micro\Collection;

class LayerCollection extends Collection
{
    public function __construct()
    {
        $this->setHandler('\PetaKami\Controllers\LayerController', true);
        $this->setPrefix('/layer');

        $this->get('/{workspace}/{layer}/geojson', 'getFeatureCollectionGeoJson');
        #$this->get('/{workspace}/{drawType}/geojson', 'getLayerDrawTypeInFormatGeoJson');
        $this->get('/{workspace}/{layerGroupName}/bbox', 'getBbox');
        $this->get('/{workspace}/{layerGroupName}/{layer}/drawtype', 'getDrawType');
        $this->get('/{workspace}/{layerGroupName}', 'getLayerAndDrawType');
        $this->get('/{workspace}', 'getLayersFromWorkspace');

        $this->post('/', 'postLayer');
    }
}