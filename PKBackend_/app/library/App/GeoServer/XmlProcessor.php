<?php


namespace PetaKami\GeoServer;


use PetaKami\Constants\Services;
use Phalcon\Di\Injectable;

class XmlProcessor extends Injectable
{
    private $xml;

    private $curl;

    public function __construct()
    {
        $this->xml = new Xml($this->di->get(Services::CONFIG));
        $this->curl = new Curl($this->di->get(Services::CONFIG));
    }

    public function createLayers($groupLayers, $workspace, $nameOfDataStoreAndLayerGroup)
    {
        foreach($groupLayers as $layerName){
            $this->createDataStore($workspace, $nameOfDataStoreAndLayerGroup);

            $this->createFeatureType($workspace, $nameOfDataStoreAndLayerGroup, $layerName);
        }
        $this->createLayerGroup($workspace, $nameOfDataStoreAndLayerGroup, $groupLayers);
    }

    public function createDataStore($workspace, $dataStore)
    {
        $this->_doCurl(
            '/workspaces/' . $workspace . '/datastores.xml',
            'post',
            $this->xml->xmlDataStore($workspace, $dataStore)
        );
    }

    public function createFeatureType($workspace, $dataStore, $featureTypeName)
    {
        $this->_doCurl(
            '/workspaces/' . $workspace .
            '/datastores/' . $dataStore . '/featuretypes',
            'post',
            $this->xml->xmlFeatureType($featureTypeName)
        );
    }

    public function createLayerGroup($workspace, $layerGroupName, $groupLayers)
    {
        $this->_doCurl(
            '/layergroups',
            'post',
            $this->xml->xmlLayerGroup($workspace, $layerGroupName, $groupLayers)
        );
    }

    private function _doCurl($url, $httpMethod, $requestBody)
    {
        $this->curl->setHttpMethod($httpMethod);
        $this->curl->setUrl($url);
        $this->curl->setRequestBody($requestBody);
        $this->curl->run();
    }
}