<?php


namespace PetaKami\Services;

use PetaKami\Auth\UserAccountType;
use PetaKami\Models\User;
use PhalconRest\Constants\ErrorCodes as ErrorCodes;
use PhalconRest\Exceptions\UserException;
use PhalconRest\Mvc\Plugin;

class UserService extends Plugin
{
    protected $user = false;

    /**
     * @return User
     * @throws UserException
     */
    public function getUser()
    {
        if(!$this->user){
            $user = null;

            $session = $this->authManager->getSession();
            if($session){
                $identity = $session->getIdentity();
                $user = User::findFirst((int)$identity);
            }

            $this->user = $user;
        }

        return $this->user;
    }
}