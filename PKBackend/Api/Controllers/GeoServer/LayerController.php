<?php


namespace PetaKami\Controllers\GeoServer;

use PetaKami\Controllers\RESTController;
use PetaKami\Controllers\Tools\QueryController;

class LayerController extends RESTController
{
    private $queryBuilder;

    public function __construct()
    {
        parent::__construct();
        $this->queryBuilder = new QueryController();
    }

    public function get()
    {
        return $this->respond(['hellow']);
    }

    public function putAction()
    {

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

        foreach($request->typ as $typ=>$val){
            if(strtolower($typ) == 'point') $this->queryBuilder->table = str_replace(' ','_',$request->name) . '_point';
            else if(strtolower($typ) == 'line') $this->queryBuilder->table = str_replace(' ','_',$request->name) . $request->name . '_line';
            else if(strtolower($typ) == 'polygon') $this->queryBuilder->table = str_replace(' ','_',$request->name) . $request->name . '_poly';

            $this->_setupQuery($typ, $val);

            $this->queryBuilder->createTablePoint();
            $this->queryBuilder->insertAction();
        }
    }

    private function _setupQuery($typ, $val)
    {
        $this->queryBuilder->columns = ['name', 'description'];
        switch(strtolower($typ)){
            case 'point':
                array_push($this->queryBuilder->columns, 'point');
                foreach($val as $k=>$v){
                    $this->queryBuilder->data = array_merge(
                        [[$k, $v->description, "ST_GeomFromText('POINT(".$v->lat." ".$v->long.")', 4326)"]],
                        $this->queryBuilder->data
                    );
                }
                break;
            case 'line':
                array_push($this->queryBuilder->columns, 'line');
                foreach($val as $k=>$v){
                    $this->queryBuilder->data = array_merge(
                        [[$k, $v->description, "ST_GeomFromText('MULTILINESTRING((".$v->lat." ".$v->long."))', 4326)"]],
                        $this->queryBuilder->data
                    );
                }
                break;
            case'polygon':
                array_push($this->queryBuilder->columns, 'polygon');
                foreach($val as $k=>$v){
                    $this->queryBuilder->data = array_merge(
                        [[$k, $v->description, "ST_GeomFromText('MULTIPOLYGON(".$v->lat." ".$v->long.")', 4326)"]],
                        $this->queryBuilder->data
                    );
                }
                break;
        }
    }
}