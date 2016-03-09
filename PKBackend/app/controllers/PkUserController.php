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
        $data = $this->request->getJsonRawBody();
        $email = $data->email;
        $password = $data->password;

        $session = $this->authManager->loginWithUsernamePassword(UserAccountType::EMAIL, $email, $password);
        $response = [
            'token'     => $session->getToken(),
            'expires'   => $session->getExpirationTime()
        ];

        return $this->respond($response);
    }

    public function register()
    {
        $data = $this->request->getJsonRawBody();

//        $user = User::findFirst([
//            'conditions' => 'email = :email:',
//            'bind'      => ['email' => $data->email]
//        ]);
//
//        if($user){
//            return $this->respond(['error' => true, 'message' => 'User Already Exist!']);
//        }

        $user = new User();
        $user->email = $data->email;
        $user->fullName = $data->fullName;
        $user->password = $this->hash->hash($data->password);

        if (!$user->save()) {
            throw new UserException(ErrorCodes::DATA_FAIL, 'Could not save users.');
        }

        $session = $this->authManager->loginWithUsernamePassword(UserAccountType::EMAIL, $data->email, $data->password);
        $response = [
            'token'     => $session->getToken(),
            'expires'   => $session->getExpirationTime()
        ];

        return $this->respond($response);
        #return $this->respondItem($user, new UserTransformer, 'user');
    }
}