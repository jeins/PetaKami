<?php


namespace PetaKami\Transformers;

use League\Fractal\TransformerAbstract;
use PetaKami\Models\User;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user){
        return [
            'id'        => (int)$user->id,
            'email'     => $user->email,
            'fullName' => $user->fullName,
        ];
    }
}