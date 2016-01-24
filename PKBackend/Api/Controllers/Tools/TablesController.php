<?php


namespace PetaKami\Controllers\Tools;

use PetaKami\Controllers\BaseController;
use Phalcon\Db\Column;

class TablesController extends BaseController
{

    public $connection;

    public $table;

    public function __construct()
    {
        parent::__construct();
        $this->connection = $this->di->getShared('db');
    }

    public function setTable()
    {

    }

    public function createTablePoint()
    {
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
                        "point",
                        [
                            "type"  =>  'geometry(Point,4326)',
                        ]
                    )
                )
            )
        );
    }

    private function _validate()
    {

    }
}