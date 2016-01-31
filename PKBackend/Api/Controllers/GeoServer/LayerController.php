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
            $drawTypes = $this->getLayersWithDrawType($workspace, $response->name);
            $drawType = '';
            foreach($drawTypes as $dType){
                $dType = explode('_', $dType);
                $drawType .=  $dType[count($dType)-1].'_';
            }
            $drawType = rtrim($drawType, '_');
            array_push($newLayerGroups, [$response->name, $drawType]);
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
        $request->name = strtolower(str_replace(' ','_',$request->name));

        $layerNames = [];
        $index = 0;

        foreach($request->type as $type=>$val){
            $this->_setupTableName($request->name, $type);
            $this->_setupColumnAndData($type, $val);

            $layerNames[$index] = $this->queryBuilder->table;
            $index++;

            $this->queryBuilder->createTable($type);
            $this->queryBuilder->insertAction();
        }
        $this->xmlRequestProcessor->createLayer($layerNames, $request->name);

        return ["OK"];
    }

    private function _setupColumnAndData($type, $val)
    {
        $this->queryBuilder->columns = [];
        array_push($this->queryBuilder->columns, 'id');
        $this->queryBuilder->data= [];

        switch(strtolower($type)){
            case 'point':
                array_push($this->queryBuilder->columns, 'point');
                $this->_mergeColumnAndData($val, 'POINT');
                break;
            case 'line':
                array_push($this->queryBuilder->columns, 'line');
                $this->_mergeColumnAndData($val, 'LINESTRING');
                break;
            case'poly':
                array_push($this->queryBuilder->columns, 'poly');
                $this->_mergeColumnAndData($val, 'POLYGON');
                break;
        }
    }

    private function _setupTableName($name, $typ)
    {
        if(strtolower($typ) == 'point') $this->queryBuilder->table = $name . '_point';
        else if(strtolower($typ) == 'line') $this->queryBuilder->table = $name . '_line';
        else if(strtolower($typ) == 'poly') $this->queryBuilder->table = $name . '_poly';
    }

    private function _mergeColumnAndData($value, $type)
    {
        for($i=0; $i<count($value); $i++){
            $geom = 'ST_GeomFromText(\''.$type.'(';
            if($type == 'POLYGON') $geom .= '(';

            foreach($value[$i] as $valA){
                if(is_array($valA)){
                    foreach($valA as $valB){
                        if(is_array($valB)){
                            foreach($valB as $valC){
                                $geom .= $valB[1].' '.$valB[0].',';
                                break;
                            }
                        } else{
                            $geom .= $valA[1].' '.$valA[0].',';
                            break;
                        }
                    }
                } else{
                    $geom .= $value[$i][1].' '.$value[$i][0].',';
                    break;
                }
            }
            $geom = rtrim($geom, ',');
            if($type == 'POLYGON') $geom .= ')';
            $geom .= ')\', 4326)';

            $this->queryBuilder->data = array_merge(
                $this->queryBuilder->data,
                [[$i+1, $geom]]
            );
        }
    }
}