<?php


namespace PetaKami\Controllers\GeoServer;

use PetaKami\Controllers\RESTController;
use PetaKami\Controllers\Tools\CurlController;
use PetaKami\Controllers\Tools\QueryController;
use PetaKami\Controllers\Tools\XmlController;

class LayerController extends RESTController
{
    private $queryBuilder;

    private $xml;

    private $curl;

    public function __construct()
    {
        parent::__construct();
        $this->queryBuilder = new QueryController();
        $this->xml = new XmlController();
        $this->curl = new CurlController();
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

        $layerNames = [];
        $index = 0;
        $request->name = str_replace(' ','_',$request->name);

        foreach($request->typ as $typ=>$val){
            if(strtolower($typ) == 'point') $this->queryBuilder->table = $request->name . '_point';
            else if(strtolower($typ) == 'line') $this->queryBuilder->table = $request->name . '_line';
            else if(strtolower($typ) == 'polygon') $this->queryBuilder->table = $request->name . '_poly';

            $this->_setupQuery($typ, $val);

            $layerNames[$index] = $this->queryBuilder->table;
            $index++;

            $this->queryBuilder->createTable($typ);
            $this->queryBuilder->insertAction();
        }

        foreach($layerNames as $layerName){
            $this->xml->workspace = 'IDBangunan'; //TODO: set static
            $this->xml->dataStore = $request->name;
            $this->xml->layerGroupName = $request->name;
            $this->xml->featureTypeName = $layerName;

            $this->_doCurl(
                $this->config->geoserver->REST_URL . '/workspaces/' . $this->xml->workspace . '/datastores.xml',
                'post',
                $this->xml->dataStoreXML()
            );

            $this->_doCurl(
                $this->config->geoserver->REST_URL . '/workspaces/' . $this->xml->workspace .
                '/datastores/' . $request->name . '/featuretypes',
                'post',
                $this->xml->featureTypeXML()
            );
        }
        $this->xml->layerGroupLayers = $layerNames;

        $this->_doCurl(
            $this->config->geoserver->REST_URL . '/layergroups',
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

    private function _setupQuery($typ, $val)
    {
        $this->queryBuilder->columns = ['name', 'description'];
        $this->queryBuilder->data= [];
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
                        [[$k, $v->description, "ST_GeomFromText('POINT(".$v->lat." ".$v->long.")', 4326)"]],
                        $this->queryBuilder->data
                    );
                }
                break;
            case'polygon':
                array_push($this->queryBuilder->columns, 'polygon');
                foreach($val as $k=>$v){
                    $this->queryBuilder->data = array_merge(
                        [[$k, $v->description, "ST_GeomFromText('POINT(".$v->lat." ".$v->long.")', 4326)"]],
                        $this->queryBuilder->data
                    );
                }
                break;
        }
    }
}