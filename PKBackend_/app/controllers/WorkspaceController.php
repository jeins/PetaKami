<?php


namespace PetaKami\Controllers;

use PetaKami\Constants\PKConst;
use PetaKami\Mvc\BaseController;

class WorkspaceController extends BaseController
{
    public function onConstruct()
    {
        parent::onConstruct();
    }

    public function all()
    {
        $workspaces = [];
        foreach($this->config->geoserver->workspaces as $workspace => $drawTyp){
            array_push($workspaces, $workspace);
        }
        return $this->respondArray($workspaces, PKConst::RESPONSE_KEY);
    }

    public function findDrawTyp($workspace)
    {
        $drawTypes = $this->config->geoserver->workspaces[$workspace];
        return $this->respondArray($drawTypes, PKConst::RESPONSE_KEY);
    }
}