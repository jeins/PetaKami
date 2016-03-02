<?php


namespace PetaKami\Models;

use PetaKami\Mvc\BaseModel;

class Layer extends BaseModel
{

    public $id;
    public $userId;
    public $name;
    public $description;

    public function initialize()
    {
        $this->belongsTo('userId', User::class, 'id', [
           'alias' => 'UserLayer'
        ]);
    }

    public function getSource()
    {
        return 'layers';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id'            => 'id',
            'user_id'       => 'userId',
            'name'          => 'name',
            'description'   => 'description'
        ];
    }
}