<?php


namespace PetaKami\Controllers;

use PetaKami\Auth\UserAccountType;
use PetaKami\Models\User;
use PetaKami\Mvc\BaseController;
use PetaKami\Transformers\UserTransformer;
use Phalcon\Security;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exceptions\UserException;

class PkUserController extends BaseController
{
    public function me()
    {
        return $this->respondItem($this->user, new UserTransformer, 'user');
    }

    public function authenticate()
    {
        $email = $this->request->getUsername();
        $password = $this->request->getPassword();

        $session = $this->authManager->loginWithUsernamePassword(UserAccountType::EMAIL, $email, $password);
        $response = [
            'token'     => $session->getToken(),
            'expires'   => $session->getExpirationTime()
        ];

        return $this->respondArray($response, 'data');
    }

    public function register()
    {
        $data = $this->request->getJsonRawBody();

        $user = new User();
        $user->email = $data->email;
        $user->fullName = $data->fullName;
        $user->password = $this->hash->hash($data->password);

        if (!$user->save()) {
            throw new UserException(ErrorCodes::DATA_FAIL, 'Could not save users.');
        }

        return $this->respondItem($user, new UserTransformer, 'user');
    }
}