<?php


namespace PetaKami\Controllers\GeoServer;

use PetaKami\Controllers\BaseController;
use PetaKami\Controllers\Tools\CurlController;
use PetaKami\Controllers\Tools\QueryController;
use PetaKami\Processors\XmlRequestProcessor;

class LayerController extends BaseController
{
    private $queryBuilder;

    private $xmlRequestProcessor;

    private $curlController;

    public function onConstruct()
    {
        $this->xmlRequestProcessor = new XmlRequestProcessor($this->di->get('config'));
        $this->queryBuilder = new QueryController();
        $this->curlController = new CurlController();
    }

    public function getLayersInGeoJSON($workspace, $layerGroupName)
    {
        $layers = $this->getLayersWithDrawType($workspace, $layerGroupName);

        $featureTypes = [];

        foreach($layers as $layer){
            $this->curlController->setUrl('/'.$workspace.
                '/ows?service=WFS&version=1.0.0&request=GetFeature&typeName='
                .$workspace.':'.$layer.
                '&maxFeatures=50&outputFormat=application%2Fjson', true);
            $this->curlController->run();
            array_push($featureTypes, [$layer => json_decode($this->curlController->responseBody[0])]);
        }

        return $featureTypes;
    }

    public function getLayerDrawTypeInGeoJSON($workspace, $layerGroupName, $drawType)
    {
        $this->curlController->setUrl('/'.$workspace.
            '/ows?service=WFS&version=1.0.0&request=GetFeature&typeName='
            .$workspace.':'.$layerGroupName.'_'.$drawType.
            '&maxFeatures=50&outputFormat=application%2Fjson', true);
        $this->curlController->run();

        return json_decode($this->curlController->responseBody[0]);
    }

    public function getLayerByWorkspace($workspace)
    {
        $this->curlController->setUrl('/workspaces/'.$workspace.'/layergroups.json');
        $this->curlController->run();

        $responses = json_decode($this->curlController->responseBody[0]);

        $newLayerGroups = [];
        foreach($responses->layerGroups->layerGroup as $response){
            array_push($newLayerGroups, $response->name);
        }

        return $newLayerGroups;
    }

    public function getLayersWithDrawType($workspace, $layerGroupName)
    {
        $this->curlController->setUrl('/workspaces/'.$workspace.'/layergroups/'.$layerGroupName.'.json');
        $this->curlController->run();

        $responses = json_decode($this->curlController->responseBody[0]);

        $newLayerGroups = [];
        foreach($responses->layerGroup->publishables->published as $response){
            array_push($newLayerGroups, $response->name);
        }

        return $newLayerGroups;
    }

    public function putAction($id)
    {
        $request = $this->getRequestBody();
        $request->name = str_replace(' ', '_', $request->name);
        $response = [];

        foreach($request->typ as $typ=>$val){
            $this->_setupTableName($request->name, $typ);
            $this->_setupColumnAndData($typ, $val);

            array_push($response, $this->queryBuilder->updateAction());
        }

        return $response;
    }

    /**
     * Contoh Request Body
     * {
        "name": "titik jarak",
        "workspace": "IDBangunan",
        "typ": {
                "point": {
                    "0": {"description":"abc bangunan", "lat": "-6.215340", "long": "106.851901"},
                    "bcd": {"description":"bcd bangunan", "lat": "-6.215340", "long": "106.851901"}
                },
                "line": {
                    "abc": {"description":"abc bangunan", "lat": "-6.215340", "long": "106.851901"},
                    "bcd": {"description":"bcd bangunan", "lat": "-6.215340", "long": "106.851901"}
                },
                "polygon":{
                    "abc": {"description":"abc bangunan", "lat": "-6.215340", "long": "106.851901"},
                    "bcd": {"description":"bcd bangunan", "lat": "-6.215340", "long": "106.851901"}
                }
            }
        }
     */
    public function postAction()
    {
        $request = $this->getRequestBody();
        $request->name = str_replace(' ','_',$request->name);

        $layerNames = [];
        $index = 0;

        foreach($request->typ as $typ=>$val){
            $this->_setupTableName($request->name, $typ);
            $this->_setupColumnAndData($typ, $val);

            $layerNames[$index] = $this->queryBuilder->table;
            $index++;

            $this->queryBuilder->createTable($typ);
            $this->queryBuilder->insertAction();
        }

        $this->xmlRequestProcessor->createLayer($layerNames, $request->name);

        return ["OK"];
    }

    private function _setupColumnAndData($typ, $val)
    {
        array_push($this->queryBuilder->columns, 'id', 'name', 'description');
        $this->queryBuilder->data= [];

        switch(strtolower($typ)){
            case 'point':
                array_push($this->queryBuilder->columns, 'point');
                $this->_mergeColumnAndData($val);
                break;
            case 'line':
                array_push($this->queryBuilder->columns, 'line');
                $this->_mergeColumnAndData($val);
                break;
            case'polygon':
                array_push($this->queryBuilder->columns, 'polygon');
                $this->_mergeColumnAndData($val);
                break;
        }
    }

    private function _setupTableName($name, $typ)
    {
        if(strtolower($typ) == 'point') $this->queryBuilder->table = $name . '_point';
        else if(strtolower($typ) == 'line') $this->queryBuilder->table = $name . '_line';
        else if(strtolower($typ) == 'polygon') $this->queryBuilder->table = $name . '_poly';
    }

    private function _mergeColumnAndData($value)
    {
        foreach($value as $k=>$v){
            $id = explode('.', $k)[1];
            $this->queryBuilder->data = array_merge(
                [[$id, $v->name , $v->description, "ST_GeomFromText('POINT(".$v->lat." ".$v->long.")', 4326)"]],
                $this->queryBuilder->data
            );
        }
    }
}