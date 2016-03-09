<?php


namespace PetaKami\Mvc;

use League\Fractal\Serializer\ArraySerializer;

class CustomSerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        if($resourceKey == 'parent') return $data;

        return [$resourceKey ?: 'data' => $data];
    }

    public function item($resourceKey, array $data)
    {
        if($resourceKey == 'parent') return $data;

        return [$resourceKey ?: 'data' => $data];
    }
}