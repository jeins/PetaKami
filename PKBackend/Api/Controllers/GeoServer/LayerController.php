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
        $this->queryBuilder->table = "atestrest";
        //$this->queryBuilder->createTablePoint("atestrest");

        return $this->respond([$this->queryBuilder->postAction()]);
    }

    public function putAction()
    {

    }

    public function postAction()
    {
        $request = $this->getRequestBody();
        $this->queryBuilder->table = $request->table;
        $this->queryBuilder->columns = ['name', 'description', 'point'];
        $this->queryBuilder->data = [
            ['helo', 'test structur', "ST_GeomFromText('POINT(-6.215340 106.851901)', 4326)"],
            ['helo', 'test structur', "ST_GeomFromText('POINT(-6.215340 106.851901)', 4326)"]
        ];
    }
}