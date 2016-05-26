<?php


namespace PetaKami\Models;

use PetaKami\Mvc\BaseModel;

class User extends BaseModel
{
    public $id;
    public $fullName;
    public $email;
    public $hash;
    public $active;
    public $password;

    /**
     * initialize user
     */
    public function initialize()
    {
        $this->hasMany('id', Layer::class, 'userId', [
           'alias' => 'Layers'
        ]);
    }

    /**
     * set table 
     * @return string
     */
    public function getSource()
    {
        return 'users';
    }

    /**
     * setup column
     * @return array
     */
    public function columnMap()
    {
        return parent::columnMap() + [
            'id'            => 'id',
            'email'         => 'email',
            'full_name'     => 'fullName',
            'hash'          => 'hash',
            'active'        => 'active',
            'password'      => 'password'
        ];
    }
}