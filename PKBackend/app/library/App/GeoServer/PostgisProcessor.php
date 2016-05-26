<?php


namespace PetaKami\GeoServer;

use PetaKami\Constants\GeoServer;
use PetaKami\Constants\PKConst;
use Phalcon\Di\Injectable;

class PostgisProcessor extends Injectable
{
    private $queryBuilder;

    /**
     * PostgisProcessor constructor.
     */
    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder($this->di->get(PKConst::DB_GEO));
    }

    /**
     * add layer to postgis
     * 
     * @param $layerName
     * @param $drawTypeAndCoordinates
     * @return array
     */
    public function addLayerToPostgis($layerName, $drawTypeAndCoordinates){
        $layerNames = [];
        $index = 0;

        foreach($drawTypeAndCoordinates as $drawType=>$coordinates){
            $this->_setupTableName($layerName, $drawType);
            $this->_setupColumnAndData($drawType, $coordinates);

            $layerNames[$index] = $this->queryBuilder->table;
            $index++;

            $this->queryBuilder->createTable($drawType);
            $this->queryBuilder->insertAction();
        }
        return $layerNames;
    }

    /**
     * update layer to postgis
     * 
     * @param $layerName
     * @param $layers
     * @param $drawTypeAndCoordinates
     * @return array
     */
    public function updateLayerToPostgis($layerName, $layers, $drawTypeAndCoordinates){
        $layerArr = explode(',', $layers);
        $layerNames = [];
        $index = 0;

        foreach($drawTypeAndCoordinates as $drawType=>$coordinates){
            if(empty($layerArr[$index])) continue;

            $this->_setupColumnAndData($drawType, $coordinates);

            if($this->queryBuilder->isTableExist($layerArr[$index])){
                $this->queryBuilder->table = $layerArr[$index];
                $this->queryBuilder->clearTable();
            } else{
                $this->_setupTableName($layerName, $drawType);
                $this->queryBuilder->createTable($drawType);

                $layerNames[$index] = $this->queryBuilder->table;
            }

            $this->queryBuilder->insertAction();
            $index++;
        }
        return $layerNames;
    }

    private function _setupColumnAndData($type, $coordinates)
    {
        $this->queryBuilder->columns = [];
        array_push($this->queryBuilder->columns, 'id');
        $this->queryBuilder->data= [];

        switch(strtolower($type)){
            case GeoServer::POINT:
                array_push($this->queryBuilder->columns, GeoServer::POINT);
                $this->_mergeColumnAndData($coordinates, strtoupper(GeoServer::POINT));
                break;
            case GeoServer::LINESTRING:
                array_push($this->queryBuilder->columns, GeoServer::LINESTRING);
                $this->_mergeColumnAndData($coordinates, strtoupper(GeoServer::LINESTRING));
                break;
            case GeoServer::POLYGON:
                array_push($this->queryBuilder->columns, GeoServer::POLYGON);
                $this->_mergeColumnAndData($coordinates, strtoupper(GeoServer::POLYGON));
                break;
        }
    }

    private function _setupTableName($layerName, $drawType)
    {
        if(strtolower($drawType) == GeoServer::POINT) $this->queryBuilder->table = $layerName . '_' . GeoServer::POINT;
        else if(strtolower($drawType) == GeoServer::LINESTRING) $this->queryBuilder->table = $layerName . '_' . GeoServer::LINESTRING;
        else if(strtolower($drawType) == GeoServer::POLYGON) $this->queryBuilder->table = $layerName . '_' . GeoServer::POLYGON;
    }

    private function _mergeColumnAndData($coordinates, $drawType)
    {
        for($i=0; $i<count($coordinates); $i++){
            $geom = 'ST_GeomFromText(\''.$drawType.'(';
            if($drawType == strtoupper(GeoServer::POLYGON)) $geom .= '(';

            foreach($coordinates[$i] as $valA){
                if(is_array($valA)){
                    foreach($valA as $valB){
                        if(is_array($valB)){
                            foreach($valB as $valC){
                                $geom .= $valB[0].' '.$valB[1].',';
                                break;
                            }
                        } else{
                            $geom .= $valA[0].' '.$valA[1].',';
                            break;
                        }
                    }
                } else{
                    $geom .= $coordinates[$i][0].' '.$coordinates[$i][1].',';
                    break;
                }
            }
            $geom = rtrim($geom, ',');
            if($drawType == strtoupper(GeoServer::POLYGON)) $geom .= ')';
            $geom .= ')\', 4326)';

            $this->queryBuilder->data = array_merge(
                $this->queryBuilder->data,
                [[$i+1, $geom]]
            );
        }
    }
}