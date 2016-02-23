<?php


namespace PetaKami\Models;

use PetaKami\Mvc\BaseModel;

class User extends BaseModel
{
    public $id;
    public $fullName;
    public $email;
    public $password;

    public function getSource()
    {
        return 'users';
    }

    public function columnMap()
    {
        return [
            'id'            => 'id',
            'full_name'     => 'fullName',
            'email'         => 'email',
            'password'      => 'password',
            'created_at'    => 'createdAt',
            'updated_at'    => 'updatedAt'
        ];
    }

    public function whiteList()
    {
        return [
            'fullName',
            'email',
            'password'
        ];
    }
}