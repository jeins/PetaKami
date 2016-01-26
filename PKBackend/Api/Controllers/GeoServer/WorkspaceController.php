<?php


namespace PetaKami\Controllers\GeoServer;


use PetaKami\Controllers\RESTController;

class WorkspaceController extends RESTController
{

    private $workspaceWithDrawTyp;

    public function __construct()
    {
        parent::__construct();
        $this->workspaceWithDrawTyp = $this->di->get('config')->geoserver->WORKSPACE;
    }

    public function getWorkspaces()
    {
        $workspaces = [];
        foreach($this->workspaceWithDrawTyp as $workspace => $drawTyp){
            array_push($workspaces, $workspace);
        }

        return $this->respond($workspaces);
    }

    public function getWorkspaceWithDrawTyp($workspace)
    {
        return $this->respond($this->workspaceWithDrawTyp->$workspace);
    }
}