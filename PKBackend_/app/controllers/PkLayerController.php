<?php


namespace PetaKami\Controllers;

use PetaKami\Models\Layer;
use PetaKami\Mvc\BaseController;
use PetaKami\Transformers\UserLayerTransformer;
use PetaKami\Constants\PKConst;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exceptions\UserException;

class PkLayerController extends BaseController
{
    public function addUserLayer()
    {
        $data = $this->request->getJsonRawBody();

        $uLayer = new Layer();
        $uLayer->userId = $this->user->id;
        $uLayer->name = $data->name;
        $uLayer->workspace = $data->workspace;
        $uLayer->description = $data->description;


        if (!$uLayer->save()) {
            throw new UserException(ErrorCodes::DATA_FAIL, 'Could not save user layer.');
        }

        return $this->respondItem($uLayer, new UserLayerTransformer, PKConst::RESPONSE_ULAYERS);
    }

    public function getAll()
    {
        $userLayer = Layer::find();

        return $this->respondCollection($userLayer, new UserLayerTransformer(), PKConst::RESPONSE_ULAYERS);
    }

    public function getByUser()
    {
        $uLayer = Layer::find([
            "userId" => $this->user->id
        ]);

        if (!$uLayer) {
            throw new UserException(ErrorCodes::DATA_NOTFOUND, 'Product with id: #' . (int)$userId . ' could not be found.');
        }

        return $this->respondCollection($uLayer, new UserLayerTransformer(), PKConst::RESPONSE_ULAYERS);
    }

    public function getByWorkspace($workspace)
    {
        $uLayer = Layer::find([
            "workspace" => $workspace
        ]);

        if (!$uLayer) {
            throw new UserException(ErrorCodes::DATA_NOTFOUND, 'Product with workspace: #' . (int)$workspace . ' could not be found.');
        }

        return $this->respondCollection($uLayer, new UserLayerTransformer(), PKConst::RESPONSE_ULAYERS);
    }
}