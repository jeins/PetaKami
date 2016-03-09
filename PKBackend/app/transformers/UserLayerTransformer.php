<?php


namespace PetaKami\Transformers;

use PetaKami\Models\Layer;

class UserLayerTransformer extends ModelTransformer
{
    protected $modelClass = Layer::class;

    protected $availableIncludes = [
        'user'
    ];

    public function includeUser(Layer $layer){
        return $this->item($layer->getUser(), new UserTransformer(), 'parent');
    }
}