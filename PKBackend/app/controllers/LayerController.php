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

    /**
     * the constructor
     */
    public function onConstruct()
    {
        parent::onConstruct();

        $this->xmlProcessor = new XmlProcessor();
        $this->postgisProcessor = new PostgisProcessor();
        $this->jsonProcessor = new JsonRequestProcessor();
        $this->uploadProcessor = new UploadProcessor();
    }

    /**
     * setup geoserver
     *
     * @return mixed
     */
    public function geoserver()
    {
        $url = str_replace('rest', '', $this->di->get(PKConst::CONFIG)->geoserver->rest_url);

        return $this->respondArray([$url], PKConst::RESPONSE_KEY);
    }

    /**
     * get feature collection with return type as GeoJSON
     *
     * @param $workspace
     * @param $layerGroupName
     * @return array
     */
    public function getFeatureCollectionGeoJson($workspace, $layerGroupName)
    {
        $geoJson = $this->jsonProcessor->featureCollection($workspace, $layerGroupName);

        return $geoJson;
    }

    /**
     * get feature collection filter by layer and workspace
     *
     * @param $workspace
     * @param $layers
     * @return array
     */
    public function getFeatureCollectionFilterByLayer($workspace, $layers)
    {
        $geoJson = $this->jsonProcessor->featureCollection($workspace, $layers, true);

        return $geoJson;
    }

    /**
     * get layer from workspace
     *
     * @param $workspace
     * @return mixed
     */
    public function getLayersFromWorkspace($workspace)
    {
        $layers = $this->jsonProcessor->layerFilterByWorkspace($workspace);

        return $this->respondArray($layers, PKConst::RESPONSE_KEY);
    }

    /**
     * get coordinate x and y, to auto zoom
     *
     * @param $workspace
     * @param $layerGroupName
     * @return mixed
     */
    public function getBbox($workspace, $layerGroupName)
    {
        $bBox = $this->jsonProcessor->bBox($workspace, $layerGroupName);

        return $this->respondArray($bBox, PKConst::RESPONSE_KEY);
    }

    /**
     * get layer with draw type
     *
     * @param $workspace
     * @param $layerGroupName
     * @return mixed
     */
    public function getLayerAndDrawType($workspace, $layerGroupName)
    {
        $layersAndDrawType = $this->jsonProcessor->layersAndDrawTypeFromLayerGroup($workspace, $layerGroupName);

        return $this->respondArray($layersAndDrawType, PKConst::RESPONSE_KEY);
    }

    /**
     * @param $workspace
     * @param $layerGroupName
     * @param $layer
     * @return mixed
     */
    public function getDrawType($workspace, $layerGroupName, $layer)
    {
        $drawType = $this->jsonProcessor->drawTypeFilterByLayer($workspace, $layerGroupName, $layer);

        return $this->respondArray($drawType, PKConst::RESPONSE_KEY);
    }

    /**
     * add new layer
     *
     * @return mixed
     */
    public function postLayer()
    {
        $requestBody = $this->request->getJsonRawBody();
        $requestBody->name = strtolower(str_replace(' ','_', $requestBody->name));

        $groupLayers = $this->postgisProcessor->addLayerToPostgis($requestBody->name, $requestBody->type);

        $this->xmlProcessor->createLayers($groupLayers, $requestBody->workspace, $requestBody->name);

        $layersAndDrawType = $this->jsonProcessor->layersAndDrawTypeFromLayerGroup($requestBody->workspace, $requestBody->name);

        return $this->respondArray($layersAndDrawType, PKConst::RESPONSE_KEY);
    }

    /**
     * upload files shp to tmp folder
     *
     * @param $type
     * @param $key
     * @return mixed
     */
    public function postUploadFiles($type, $key)
    {
        $this->uploadProcessor->uploadFileToTmpFolder($type, $key);

        return $this->respondOK();
    }

    /**
     * execute uploaded files in tmp folder
     *
     * @param $workspace
     * @param $dataStore
     * @param $key
     * @return mixed
     */
    public function postUploadLayers($workspace, $dataStore, $key)
    {
        $this->uploadProcessor->uploadFileToGeoServer($workspace, $dataStore, $key);

        $layersAndDrawType = $this->jsonProcessor->layersAndDrawTypeFromLayerGroup($workspace, strtolower(str_replace(' ','_', $dataStore)));

        return $this->respondArray($layersAndDrawType, PKConst::RESPONSE_KEY);
    }

    /**
     * edit layer
     * 
     * @return mixed
     */
    public function editLayer()
    {
        $requestBody = $this->request->getJsonRawBody();

        $groupLayers = $this->postgisProcessor->updateLayerToPostgis($requestBody->name, $requestBody->layers, $requestBody->coordinates);

        if(!empty($groupLayers)){
            $this->xmlProcessor->createLayers($groupLayers, $requestBody->workspace, $requestBody->name);
        }

        $layersAndDrawType = $this->jsonProcessor->layersAndDrawTypeFromLayerGroup($requestBody->workspace, $requestBody->name);

        return $this->respondArray($layersAndDrawType, PKConst::RESPONSE_KEY);
    }
}