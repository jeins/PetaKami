<?php


namespace PetaKami\Controllers;

use PetaKami\Models\Layer;
use PetaKami\Mvc\BaseController;
use PetaKami\Transformers\UserLayerTransformer;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exceptions\UserException;

class PkLayerController extends BaseController
{
    public function addUserLayer()
    {
        $data = $this->request->getJsonRawBody();

        $uLayer = new Layer();
        $uLayer->userId = $data->userId;
        $uLayer->name = $data->name;
        $uLayer->description = $data->description;


        if (!$uLayer->save()) {
            throw new UserException(ErrorCodes::DATA_FAIL, 'Could not save user layer.');
        }

        return $this->respondItem($uLayer, new UserLayerTransformer, 'uLayer');
    }

    public function getAll()
    {
        $userLayer = Layer::find();

        return $this->respondCollection($userLayer, new UserLayerTransformer(), 'uLayers');
    }

    public function getByUser($userId)
    {
        $uLayer = Layer::find([
            "userId" => $userId
        ]);

        if (!$uLayer) {
            throw new UserException(ErrorCodes::DATA_NOTFOUND, 'Product with id: #' . (int)$userId . ' could not be found.');
        }

        return $this->respondCollection($uLayer, new UserLayerTransformer(), 'uLayers');
    }
}