<?php


namespace PetaKami\Controllers\Tools;

use PetaKami\Controllers\BaseController;
use Phalcon\Db\Column;

class TablesController extends BaseController
{
    public $table;

    public $connection;

    public function onConstruct()
    {
        $this->connection = $this->di->get('db');
    }

    public function createTable($typ)
    {
        if($typ == 'point') $spacialTyp = 'geometry(Point,4326)';
        else if($typ == 'line') $spacialTyp = 'geometry(LineString,4326)';
        else if($typ == 'poly') $spacialTyp = 'geometry(Polygon,4326)';

        try{
            $this->connection->createTable(
                $this->table,
                null,
                array(
                    "columns" => array(
                        new Column(
                            "id",
                            [
                                "type"          => Column::TYPE_INTEGER,
                                "notNull"       => true,
                                "autoIncrement" => true,
                                "primary"       => true,
                            ]
                        ),
//                        new Column(
//                            "name",
//                            [
//                                "type"    => Column::TYPE_VARCHAR,
//                                "size"    => 70,
//                            ]
//                        ),
//                        new Column(
//                            "description",
//                            [
//                                "type"    => Column::TYPE_TEXT,
//                            ]
//                        ),
                        new Column(
                            $typ,
                            [
                                "type"  =>  $spacialTyp,
                            ]
                        )
                    )
                )
            );
        } catch(\Exception $e){
            //TODO: need exception!
        }
    }

    private function _validate()
    {

    }
}