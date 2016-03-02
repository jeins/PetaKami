<?php


namespace PetaKami\Controllers;

use PetaKami\Constants\PKConst;
use PetaKami\GeoServer\JsonRequestProcessor;
use PetaKami\GeoServer\XmlProcessor;
use PetaKami\GeoServer\PostgisProcessor;
use PetaKami\GeoServer\UploadProcessor;
use PetaKami\Mvc\BaseController;

class LayerController extends BaseController
{
    /** @var XmlProcessor */
    protected $xmlProcessor;

    /** @var PostgisProcessor */
    protected $postgisProcessor;

    /** @var JsonRequestProcessor */
    protected $jsonProcessor;

    /** @var  UploadProcessor */
    protected $uploadProcessor;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->xmlProcessor = new XmlProcessor();
        $this->postgisProcessor = new PostgisProcessor();
        $this->jsonProcessor = new JsonRequestProcessor();
        $this->uploadProcessor = new UploadProcessor();
    }

    public function getFeatureCollectionGeoJson($workspace, $layerGroupName)
    {
        $geoJson = $this->jsonProcessor->featureCollection($workspace, $layerGroupName);

        return $geoJson;
    }

    public function getFeatureCollectionFilterByLayer($workspace, $layers)
    {
        $geoJson = $this->jsonProcessor->featureCollection($workspace, $layers, true);

        return $geoJson;
    }

    public function getLayersFromWorkspace($workspace)
    {
        $layers = $this->jsonProcessor->layerFilterByWorkspace($workspace);

        return $this->respondArray($layers, PKConst::RESPONSE_KEY);
    }

    public function getBbox($workspace, $layerGroupName)
    {
        $bBox = $this->jsonProcessor->bBox($workspace, $layerGroupName);

        return $this->respondArray($bBox, PKConst::RESPONSE_KEY);
    }

    public function getLayerAndDrawType($workspace, $layerGroupName)
    {
        $layersAndDrawType = $this->jsonProcessor->layersAndDrawTypeFromLayerGroup($workspace, $layerGroupName);

        return $this->respondArray($layersAndDrawType, PKConst::RESPONSE_KEY);
    }

    public function getDrawType($workspace, $layerGroupName, $layer)
    {
        $drawType = $this->jsonProcessor->drawTypeFilterByLayer($workspace, $layerGroupName, $layer);

        return $this->respondArray($drawType, PKConst::RESPONSE_KEY);
    }

    public function postLayer()
    {
        $requestBody = $this->request->getJsonRawBody();
        $requestBody->name = strtolower(str_replace(' ','_', $requestBody->name));

        $groupLayers = $this->postgisProcessor->addLayerToPostgis($requestBody->name, $requestBody->type);

        $this->xmlProcessor->createLayers($groupLayers, $requestBody->workspace, $requestBody->name);

        $layersAndDrawType = $this->jsonProcessor->layersAndDrawTypeFromLayerGroup($requestBody->workspace, $requestBody->name);

        return $this->respondArray($layersAndDrawType, PKConst::RESPONSE_KEY);
    }

    public function postUploadFiles($type, $key)
    {
        $this->uploadProcessor->uploadFileToTmpFolder($type, $key);

        return $this->respondOK();
    }

    public function postUploadLayers($workspace, $dataStore, $key)
    {
        $this->uploadProcessor->uploadFileToGeoServer($workspace, $dataStore, $key);

        $layersAndDrawType = $this->jsonProcessor->layersAndDrawTypeFromLayerGroup($workspace, strtolower(str_replace(' ','_', $dataStore)));

        return $this->respondArray($layersAndDrawType, PKConst::RESPONSE_KEY);
    }
}