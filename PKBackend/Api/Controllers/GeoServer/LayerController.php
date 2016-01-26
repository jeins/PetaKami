<?php


namespace PetaKami\Controllers\GeoServer;

use PetaKami\Controllers\RESTController;
use PetaKami\Controllers\Tools\CurlController;
use PetaKami\Controllers\Tools\QueryController;
use PetaKami\Controllers\Tools\XmlController;
use PetaKami\Processors\GeoServer\LayerProcessor;

class LayerController extends RESTController
{
    private $queryBuilder;

    private $layerProcessor;

    public function __construct()
    {
        parent::__construct();
        $this->layerProcessor = new LayerProcessor($this->config);
        $this->queryBuilder = new QueryController();
    }

    public function get()
    {
        return $this->respond(['hellow']);
    }

    public function putAction($id)
    {
        $request = $this->getRequestBody();
        $request->name = str_replace(' ', '_', $request->name);
        $this->queryBuilder->columns = ['id'];

        foreach($request->typ as $typ=>$val){
            $this->_setupQuery($typ, $val, $request->name);
            $this->queryBuilder->updateAction();
        }
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
            $this->_setupQuery($typ, $val, $request->name);

            $layerNames[$index] = $this->queryBuilder->table;
            $index++;

            $this->queryBuilder->createTable($typ);
            $this->queryBuilder->insertAction();
        }

        $this->layerProcessor->createLayer($layerNames, $request->name);

        $this->respond(["OK"]);
    }

    private function _setupQuery($typ, $val, $tblName)
    {
        array_push($this->queryBuilder->columns, 'name', 'description');
        $this->queryBuilder->data= [];

        if(strtolower($typ) == 'point') $this->queryBuilder->table = $tblName . '_point';
        else if(strtolower($typ) == 'line') $this->queryBuilder->table = $tblName . '_line';
        else if(strtolower($typ) == 'polygon') $this->queryBuilder->table = $tblName . '_poly';

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

    private function _mergeColumnAndData($value)
    {
        foreach($value as $k=>$v){
            $this->queryBuilder->data = array_merge(
                [[$k, $v->name , $v->description, "ST_GeomFromText('POINT(".$v->lat." ".$v->long.")', 4326)"]],
                $this->queryBuilder->data
            );
        }
    }
}