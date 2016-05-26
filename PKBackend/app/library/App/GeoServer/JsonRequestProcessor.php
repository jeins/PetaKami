<?php


namespace PetaKami\GeoServer;


use PetaKami\Constants\GeoServer;
use PetaKami\Constants\PKConst;
use Phalcon\Di\Injectable;

class JsonRequestProcessor extends Injectable
{

    private $curl;

    /**
     * JsonRequestProcessor constructor.
     */
    public function __construct()
    {
        $this->curl = new Curl($this->di->get(PKConst::CONFIG));
    }

    /**
     * get layer from layer group
     * 
     * @param $workspace
     * @param $dataStore
     * @return array
     */
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

    /**
     * get layer with draw type from layer group
     * 
     * @param $workspace
     * @param $layerGroupName
     * @return array
     */
    public function layersAndDrawTypeFromLayerGroup($workspace, $layerGroupName)
    {
        $layers = $this->layersFromLayerGroup($workspace, $layerGroupName);

        $layersAndDrawTypes = [];

        foreach ($layers as $layer) {
            $drawType = $this->drawTypeFilterByLayer($workspace, $layerGroupName, $layer);
            array_push($layersAndDrawTypes, ['layer' => $drawType['layer'], 'drawType' => $drawType['drawType']]);
        }

        return $layersAndDrawTypes;
    }

    /**
     * get layer by specific workspace
     * 
     * @param $workspace
     * @return array
     */
    public function layerFilterByWorkspace($workspace)
    {
        $responses = json_decode($this->_doCurl('/workspaces/'.$workspace.'/layergroups.json'));

        $newLayerGroups = [];

        foreach($responses->layerGroups->layerGroup as $response){
            $layers = $this->layersFromLayerGroup($workspace, $response->name);
            $tmpLayer = [];
            foreach($layers as $layer){
                $drawTypes = $this->drawTypeFilterByLayer($workspace, $response->name, $layer);
                array_push($tmpLayer, ['layer' => $drawTypes['layer'], 'drawType' => $drawTypes['drawType']]);
            }
            $newLayerGroups[$response->name] = $tmpLayer;
        }

        return $newLayerGroups;
    }

    /**
     * get draw type by specific workspace, layer group and layer
     * 
     * @param $workspace
     * @param $layerGroupName
     * @param $layer
     * @return array
     */
    public function drawTypeFilterByLayer($workspace, $layerGroupName, $layer)
    {
        $response = $this->_doCurl('/workspaces/'.$workspace.'/datastores/' . $layerGroupName . '/featuretypes/' . $layer . '.json');

        $drawType = [];

        if(strpos(strtolower($response), GeoServer::POINT) !== false) $drawType['drawType'] = GeoServer::POINT;
        if(strpos(strtolower($response), GeoServer::LINESTRING) !== false) $drawType['drawType'] = GeoServer::LINESTRING;
        if(strpos(strtolower($response), GeoServer::POLYGON) !== false) $drawType['drawType'] = GeoServer::POLYGON;

        $drawType['layer'] = $layer;

        return $drawType;
    }

    /**
     * get coordinate box for auto zoom by workspace and layer group
     * 
     * @param $workspace
     * @param $layerGroupName
     * @return array
     */
    public function bBox($workspace, $layerGroupName)
    {
        $response = $this->_doCurl(
            '/wms/reflect?format=application/openlayers&layers=' .  $workspace . ':' . $layerGroupName,
            true
        );
        $responses = preg_replace('/\s+/', '', $this->_getStringBetween($response, 'OpenLayers.Bounds(', ');'));

        return explode(',', $responses);
    }

    /**
     * get feature collection
     * 
     * @param $workspace
     * @param $layerGroupName
     * @param bool $filterByLayer
     * @return array|mixed
     */
    public function featureCollection($workspace, $layerGroupName, $filterByLayer = false)
    {
        $layers = explode(',', $layerGroupName);

        if(!$filterByLayer) $layers = $this->layersFromLayerGroup($workspace, $layerGroupName);

        $featureTypes = [];

        $isFirst = true;
        foreach($layers as $layer){

            if($layer == "") continue;

            $response = json_decode($this->_doCurl(
                '/'.$workspace.
                '/ows?service=WFS&version=1.0.0&request=GetFeature&typeName='
                .$workspace.':'.$layer.
                '&maxFeatures=50&outputFormat=application%2Fjson',
                true
            ));

            if($isFirst){
                $featureTypes = $response;
                $isFirst = false;
            } else{
                $featureTypes->features = array_merge($response->features, $featureTypes->features);
            }
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