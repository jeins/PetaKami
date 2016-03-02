<?php


namespace PetaKami\Transformers;

use PetaKami\Models\User;

class UserTransformer extends ModelTransformer
{
    protected $modelClass = User::class;

    protected function excludedProperties()
    {
        return ['password'];
    }

}