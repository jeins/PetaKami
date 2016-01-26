<?php


namespace PetaKami\Controllers\GeoServer;


use PetaKami\Controllers\BaseController;

class WorkspaceController extends BaseController
{

    private $workspaceWithDrawTyp;

    public function onConstruct()
    {
        $this->workspaceWithDrawTyp = $this->di->get('config')->geoserver->WORKSPACE;
    }

    public function getWorkspaces()
    {
        $workspaces = [];
        foreach($this->workspaceWithDrawTyp as $workspace => $drawTyp){
            array_push($workspaces, $workspace);
        }

        return $workspaces;
    }

    public function getWorkspaceWithDrawTyp($workspace)
    {
        return $this->workspaceWithDrawTyp->$workspace;
    }
}