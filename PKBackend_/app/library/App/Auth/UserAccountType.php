<?php


namespace PetaKami\Auth;

use PetaKami\Constants\PKConst;
use PetaKami\Models\User;
use Phalcon\Di;
use PhalconRest\Auth\Manager;
use PhalconRest\Auth\AccountType;

class UserAccountType implements AccountType
{
    const EMAIL = "username";

    /**
     * @param array $data Login data
     *
     * @return string Identity
     */
    public function login($data)
    {
        $security = Di::getDefault()->get(PKConst::SECURITY);

        $username = $data[Manager::LOGIN_DATA_USERNAME];
        $password = $data[Manager::LOGIN_DATA_PASSWORD];

        /** @var \PetaKami\Models\User $user */
        $user = User::findFirst([
            'conditions' => 'email = :email:',
            'bind'      => ['email' => $username]
        ]);

        if(!$user){
            return null;
        }

        if(!$security->checkHash($password, $user->password)){
            return null;
        }

        return (string)$user->id;
    }

    /**
     * @param string $identity Identity
     *
     * @return bool Authentication successful
     */
    public function authenticate($identity)
    {
        return User::existsById((int)$identity);
    }
}