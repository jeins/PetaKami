<?php


namespace PetaKami\GeoServer;


use PetaKami\Constants\PKConst;
use Phalcon\Di\Injectable;

class JsonRequestProcessor extends Injectable
{

    private $curl;

    public function __construct()
    {
        $this->curl = new Curl($this->di->get(PKConst::CONFIG));
    }

    public function layersFromLayerGroup($workspace, $dataStore)
    {
        $responses = json_decode($this->_doCurl('/workspaces/'.$workspace.'/datastores/'. $dataStore.'/featuretypes.json'));
        $featureTypes = $responses->featureTypes->featureType;
        $layerNames = [];

        foreach($featureTypes as $featureType){
            array_push ($layerNames, $featureType->name);
        }

        return $layerNames;
    }

    public function layersAndDrawTypeFromLayerGroup($workspace, $layerGroupName)
    {
        $layers = $this->layersFromLayerGroup($workspace, $layerGroupName);

        $layersAndDrawTypes = [];

        foreach ($layers as $layer) {
            $drawType = $this->drawTypeFilterByLayer($workspace, $layerGroupName, $layer);
            array_push($layersAndDrawTypes, ['layer' => $drawType[1], 'drawType' => $drawType[0]]);
        }

        return $layersAndDrawTypes;
    }

    public function layerFilterByWorkspace($workspace)
    {
        $responses = json_decode($this->_doCurl('/workspaces/'.$workspace.'/layergroups.json'));

        $newLayerGroups = [];

        foreach($responses->layerGroups->layerGroup as $response){
            $layers = $this->layersFromLayerGroup($workspace, $response->name);
            $tmpLayer = [];
            foreach($layers as $layer){
                $drawTypes = $this->drawTypeFilterByLayer($workspace, $response->name, $layer);
                array_push($tmpLayer, ['layer' => $drawTypes[1], 'drawType' => $drawTypes[0]]);
            }
            $newLayerGroups[$response->name] = $tmpLayer;
        }

        return $newLayerGroups;
    }

    public function drawTypeFilterByLayer($workspace, $layerGroupName, $layer)
    {
        $response = $this->_doCurl('/workspaces/'.$workspace.'/datastores/' . $layerGroupName . '/featuretypes/' . $layer . '.json');

        $drawType = [];

        if(strpos($response, 'Point') !== false) $drawType[0] = 'point';
        if(strpos($response, 'LineString') !== false) $drawType[0] = 'linestring';
        if(strpos($response, 'Polygon') !== false) $drawType[0] = 'polygon';

        $drawType[1] = $layer;

        return $drawType;
    }

    public function bBox($workspace, $layerGroupName)
    {
        $response = $this->_doCurl(
            '/wms/reflect?format=application/openlayers&layers=' .  $workspace . ':' . $layerGroupName,
            true
        );
        $responses = preg_replace('/\s+/', '', $this->_getStringBetween($response, 'OpenLayers.Bounds(', ');'));

        return explode(',', $responses);
    }

//    public function drawTypeFormatInGeoJson($workspace, $drawType)
//    {
//        $dTypes = explode(',', $drawType);
//
//        $isFirst = true;
//        $geoJson = [];
//        foreach($dTypes as $dType){
//            if($dType != ""){
//                $response = json_decode($this->_doCurl(
//                    '/ows?service=WFS&version=1.0.0&request=GetFeature&typeName='
//                    .$workspace.':'.$dType.
//                    '&maxFeatures=50&outputFormat=application%2Fjson',
//                    true
//                ));
//
//                if($isFirst){
//                    $geoJson = $response;
//                    $isFirst = false;
//                } else{
//                    $geoJson->features = array_merge($response->features, $geoJson->features);
//                }
//            }
//        }
//
//        return $geoJson;
//    }

    public function featureCollection($workspace, $layerGroupName)
    {
        $layers = $this->layersFromLayerGroup($workspace, $layerGroupName);

        $featureTypes = [];

        foreach($layers as $layer){
            array_push($featureTypes, [$layer => json_decode($this->_doCurl(
                '/'.$workspace.
                '/ows?service=WFS&version=1.0.0&request=GetFeature&typeName='
                .$workspace.':'.$layer.
                '&maxFeatures=50&outputFormat=application%2Fjson',
                true
            ))]);
        }

        return $featureTypes;
    }

    private function _doCurl($url, $replaceUrl = false)
    {
        $this->curl->setHttpMethod('get');

        if($replaceUrl) $this->curl->setUrl($url, $replaceUrl);
        else $this->curl->setUrl($url);

        $this->curl->run();

        return $this->curl->getResponseBody();
    }

    private function _getStringBetween($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}