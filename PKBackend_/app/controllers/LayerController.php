<?php


namespace PetaKami\Controllers;

use PetaKami\GeoServer\JsonRequestProcessor;
use PetaKami\GeoServer\XmlProcessor;
use PetaKami\GeoServer\PostgisProcessor;
use PetaKami\Mvc\BaseController;

class LayerController extends BaseController
{
    /**
     * @var \PetaKami\GeoServer\XmlProcessor
     */
    protected $xmlProcessor;

    /**
     * @var \PetaKami\GeoServer\PostgisProcessor
     */
    protected $postgisProcessor;

    /**
     * @var \PetaKami\GeoServer\JsonRequestProcessor
     */
    protected $jsonProcessor;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->xmlProcessor = new XmlProcessor();
        $this->postgisProcessor = new PostgisProcessor();
        $this->jsonProcessor = new JsonRequestProcessor();
    }

    public function getFeatureCollectionGeoJson($workspace, $layer)
    {
        $geoJson = $this->jsonProcessor->featureCollection($workspace, $layer);

        return $geoJson;
    }

//    public function getLayerDrawTypeInFormatGeoJson($workspace, $drawType)
//    {
//        $geoJson = $this->jsonProcessor->drawTypeFormatInGeoJson($workspace, $drawType);
//
//        return $geoJson;
//    }

    public function getLayersFromWorkspace($workspace)
    {
        $layers = $this->jsonProcessor->layerFilterByWorkspace($workspace);

        return $this->respondArray($layers, 'records');
    }

    public function getBbox($workspace, $layerGroupName)
    {
        $bBox = $this->jsonProcessor->bBox($workspace, $layerGroupName);

        return $this->respondArray($bBox, 'records');
    }

    public function getLayerAndDrawType($workspace, $layerGroupName)
    {
        $layersAndDrawType = $this->jsonProcessor->layersAndDrawTypeFromLayerGroup($workspace, $layerGroupName);

        return $this->respondArray($layersAndDrawType, 'records');
    }

    public function getDrawType($workspace, $layerGroupName, $layer)
    {
        $drawType = $this->jsonProcessor->drawTypeFilterByLayer($workspace, $layerGroupName, $layer);

        return $this->respondArray($drawType[0], 'records');
    }

    public function postLayer()
    {
        $requestBody = $this->request->getJsonRawBody();
        $requestBody->name = strtolower(str_replace(' ','_', $requestBody->name));

        $groupLayers = $this->postgisProcessor->addLayerToPostgis($requestBody->name, $requestBody->type);

        $this->xmlProcessor->createLayers($groupLayers, $requestBody->workspace, $requestBody->name);

        return $this->respondOK();
    }


}