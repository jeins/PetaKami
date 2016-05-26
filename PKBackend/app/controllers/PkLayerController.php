<?php


namespace PetaKami\Controllers;

use PetaKami\Models\Layer;
use PetaKami\Mvc\BaseController;
use PetaKami\Transformers\UserLayerTransformer;
use PetaKami\Constants\PKConst;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exceptions\UserException;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class PkLayerController extends BaseController
{
    /**
     * add user layer to own database
     * 
     * @return mixed
     * @throws UserException
     */
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

    /**
     * get layer informasi from own database
     * 
     * @param $limit
     * @param $currentPage
     * @return mixed
     */
    public function getAll($limit, $currentPage)
    {
        $userLayer = Layer::find();

        if($limit == 0) {
            $limit = 1000;
        }

        $paginator = new PaginatorModel([
            "data"  => $userLayer,
            "limit" => $limit,
            "page"  => $currentPage
        ]);

        $page = $paginator->getPaginate();

        foreach($page->items as $p){
            $p->allowEdit = false;
            if($p->userId == $this->user->id){
                $p->allowEdit = true;
            }
        }
        return $this->respond($page);
    }

    /**
     * get information from user
     * 
     * @return mixed
     * @throws UserException
     */
    public function getByUser()
    {
        $uLayer = Layer::find("userId='" . $this->user->id."''");

        if (!$uLayer) {
            throw new UserException(ErrorCodes::DATA_NOTFOUND, 'Product with id: #' . (int)$this->user->id . ' could not be found.');
        }

        return $this->respondCollection($uLayer, new UserLayerTransformer(), PKConst::RESPONSE_ULAYERS);
    }

    /**
     * get information from workspace
     * 
     * @param $workspace
     * @return mixed
     * @throws UserException
     */
    public function getByWorkspace($workspace)
    {
        $uLayer = Layer::find("workspace='$workspace'");

        if (!$uLayer) {
            throw new UserException(ErrorCodes::DATA_NOTFOUND, 'Product with workspace: #' . (int)$workspace . ' could not be found.');
        }

        return $this->respondCollection($uLayer, new UserLayerTransformer(), PKConst::RESPONSE_ULAYERS);
    }
}