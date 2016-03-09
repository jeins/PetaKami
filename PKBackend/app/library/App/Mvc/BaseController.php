<?php


namespace PetaKami\Mvc;

use Phalcon\Security;
use PhalconRest\Mvc\FractalController;
use PetaKami\Constants\PKConst as PKService;
use PetaKami\Services\UserService;

/**
 * PetaKami\Mvc\BaseController
 *
 * @property \PhalconRest\Http\Request $request;
 * @property \PhalconRest\Http\Response $response;
 * @property \PhalconRest\Auth\Manager $authManager
 * @property \PhalconRest\Auth\TokenParser $tokenParser
 * @property \PetaKami\Services\UserService $userService
 * @property mixed $config
 */
class BaseController extends FractalController
{

    protected $user;

    protected $config;

    public $hash;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->user = $this->userService->getUser();

        $this->config = $this->di->get(PKService::CONFIG);

        $this->hash = new Security();
    }
}