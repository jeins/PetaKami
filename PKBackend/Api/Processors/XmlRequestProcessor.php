<?php


namespace PetaKami\Processors;

use PetaKami\Controllers\Tools\CurlController;
use PetaKami\Controllers\Tools\XmlController;

class XmlRequestProcessor
{

    private $xml;
    private $curl;

    public function __construct()
    {
        $this->xml = new XmlController();
        $this->curl = new CurlController();
    }

    public function getLayer()
    {

    }

    public function createLayer($layerNames, $name)
    {
        foreach($layerNames as $layerName){
            $this->xml->workspace = 'IDBangunan'; //TODO: set static
            $this->xml->dataStore = $name;
            $this->xml->layerGroupName = $name;
            $this->xml->featureTypeName = $layerName;

            $this->_doCurl(
                '/workspaces/' . $this->xml->workspace . '/datastores.xml',
                'post',
                $this->xml->dataStoreXML()
            );

            $this->_doCurl(
                '/workspaces/' . $this->xml->workspace .
                '/datastores/' . $name . '/featuretypes',
                'post',
                $this->xml->featureTypeXML()
            );
        }
        $this->xml->layerGroupLayers = $layerNames;

        $this->_doCurl(
            '/layergroups',
            'post',
            $this->xml->layerGroupXML()
        );
    }

    private function _doCurl($url, $reqMethod, $reqBody)
    {
        $this->curl->setRequestMethod($reqMethod);
        $this->curl->setUrl($url);
        $this->curl->setRequestBody($reqBody);
        $this->curl->run();
    }
}