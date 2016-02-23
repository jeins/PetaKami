<?php


namespace PetaKami\Controllers;

use PetaKami\Auth\UserAccountType;
use PetaKami\Mvc\BaseController;
use PetaKami\Transformers\UserTransformer;

class UserController extends BaseController
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
}