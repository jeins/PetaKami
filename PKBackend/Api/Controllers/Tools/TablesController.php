<?php


namespace PetaKami\Controllers\Tools;

use PetaKami\Controllers\BaseController;
use Phalcon\Db\Column;

class TablesController extends BaseController
{
    public $table;

    public function __construct()
    {
        parent::__construct();
    }

    public function setTable()
    {

    }

    public function createTable($typ)
    {
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
                        new Column(
                            "name",
                            [
                                "type"    => Column::TYPE_VARCHAR,
                                "size"    => 70,
                            ]
                        ),
                        new Column(
                            "description",
                            [
                                "type"    => Column::TYPE_TEXT,
                            ]
                        ),
                        new Column(
                            $typ,
                            [
                                "type"  =>  'geometry(Point,4326)',
                            ]
                        )
                    )
                )
            );
        } catch(\Exception $e){
            // ADD LOG FILE
        }
    }

    private function _validate()
    {

    }
}