<?php


namespace PetaKami\Models;

use PetaKami\Mvc\BaseModel;

class Layer extends BaseModel
{

    public $id;
    public $userId;
    public $name;
    public $workspace;
    public $description;

    /**
     * initialize layer
     */
    public function initialize()
    {
        $this->belongsTo('userId', User::class, 'id', [
           'alias' => 'User'
        ]);
    }

    /**
     * set table layers
     * @return string
     */
    public function getSource()
    {
        return 'layers';
    }

    /**
     * setup column
     * @return array
     */
    public function columnMap()
    {
        return parent::columnMap() + [
            'id'            => 'id',
            'user_id'       => 'userId',
            'name'          => 'name',
            'workspace'     => 'workspace',
            'description'   => 'description'
        ];
    }
}