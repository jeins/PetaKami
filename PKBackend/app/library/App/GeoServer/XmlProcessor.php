<?php


namespace PetaKami\GeoServer;


use PetaKami\Constants\PKConst;
use Phalcon\Di\Injectable;

class XmlProcessor extends Injectable
{
    private $xml;

    private $curl;

    /**
     * XmlProcessor constructor.
     */
    public function __construct()
    {
        $this->xml = new Xml($this->di->get(PKConst::CONFIG));
        $this->curl = new Curl($this->di->get(PKConst::CONFIG));
    }

    /**
     * xml request create layers
     * 
     * @param $groupLayers
     * @param $workspace
     * @param $nameOfDataStoreAndLayerGroup
     */
    public function createLayers($groupLayers, $workspace, $nameOfDataStoreAndLayerGroup)
    {
        foreach($groupLayers as $layerName){
            $this->createDataStore($workspace, $nameOfDataStoreAndLayerGroup);

            $this->createFeatureType($workspace, $nameOfDataStoreAndLayerGroup, $layerName);
        }
        $this->createLayerGroup($workspace, $nameOfDataStoreAndLayerGroup, $groupLayers);
    }

    /**
     * create data store
     * 
     * @param $workspace
     * @param $dataStore
     */
    public function createDataStore($workspace, $dataStore)
    {
        $this->_doCurl(
            '/workspaces/' . $workspace . '/datastores.xml',
            'post',
            $this->xml->xmlDataStore($workspace, $dataStore)
        );
    }

    /**
     * create feature type
     * 
     * @param $workspace
     * @param $dataStore
     * @param $featureTypeName
     */
    public function createFeatureType($workspace, $dataStore, $featureTypeName)
    {
        $this->_doCurl(
            '/workspaces/' . $workspace .
            '/datastores/' . $dataStore . '/featuretypes',
            'post',
            $this->xml->xmlFeatureType($featureTypeName)
        );
    }

    /**
     * create layer group
     * 
     * @param $workspace
     * @param $layerGroupName
     * @param $groupLayers
     */
    public function createLayerGroup($workspace, $layerGroupName, $groupLayers)
    {
        $this->_doCurl(
            '/layergroups',
            'post',
            $this->xml->xmlLayerGroup($workspace, $layerGroupName, $groupLayers)
        );
    }

    /**
     * create layer from shp
     * 
     * @param $workspace
     * @param $dataStore
     * @param $shpFile
     */
    public function createLayerFromShp($workspace, $dataStore, $shpFile)
    {
        $this->_doCurl(
            '/workspaces/' . $workspace .'/datastores/' . $dataStore .'/file.shp',
            'put',
            $shpFile
        );
    }

    private function _doCurl($url, $httpMethod, $requestBody)
    {
        $this->curl->setUrl($url);
        $this->curl->setHttpMethod($httpMethod);
        $this->curl->setRequestBody($requestBody);
        $this->curl->run();
    }
}