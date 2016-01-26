<?php


namespace PetaKami\Controllers\GeoServer;

use PetaKami\Controllers\BaseController;
use PetaKami\Controllers\Tools\QueryController;
use PetaKami\Processors\XmlRequestProcessor;

class LayerController extends BaseController
{
    private $queryBuilder;

    private $xmlRequestProcessor;

    public function onConstruct()
    {
        $this->xmlRequestProcessor = new XmlRequestProcessor($this->di->get('config'));
        $this->queryBuilder = new QueryController();
    }

    public function get()
    {
        return 'hellow';
    }

    public function putAction($id)
    {
        $request = $this->getRequestBody();
        $request->name = str_replace(' ', '_', $request->name);

        foreach($request->typ as $typ=>$val){
            $this->_setupTableName($request->name, $typ);
            $this->_setupColumnAndData($typ, $val);

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
            $this->queryBuilder->data = array_merge(
                [[$k, $v->name , $v->description, "ST_GeomFromText('POINT(".$v->lat." ".$v->long.")', 4326)"]],
                $this->queryBuilder->data
            );
        }
    }
}