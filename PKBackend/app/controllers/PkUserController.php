<?php


namespace PetaKami\Controllers;

use PetaKami\Auth\Mail;
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

        $user = User::findFirst([
            'conditions' => 'email = :email:',
            'bind' => ['email' => $email]
        ]);

        if(!$user->active){
            return $this->respond(["error" => true, "msg" => "Account is not active, check your E-Mail!"]);
        }

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
        $user->hash = md5($this->hash->hash($data->email) . date('jS \of F Y h:i:s'));
        $user->active = false;
        $user->password = $this->hash->hash($data->password);

        if (!$user->save()) {
            throw new UserException(ErrorCodes::DATA_FAIL, 'Could not save users.');
        }

        $mail = new Mail();
        $mail->send($data->email, "Daftar", $user->fullName, $user->hash);

        return $this->respond(["data" => ["error" => false]]);
    }

    public function setUserActive($hash){
        $user = User::findFirst([
            'conditions' => 'hash = :hash:',
            'bind' => ['hash' => $hash]
        ]);

        if (!$user) {
            throw new UserException(ErrorCodes::DATA_FAIL, 'Could not found users.');
        }

        $user->active = true;
        $user->hash = '';

        if (!$user->save()) {
            throw new UserException(ErrorCodes::DATA_FAIL, 'Could not save users.');
        }

        return $this->respondOK();
    }
}