<?php

namespace App\Service\Normalizer;

class Normalizer
{
    public function normalize(array $object, array $hidden = ["updated_at", "created_at"]): array
    {
        $result = [];

        foreach($object as $key => $value) {
            if(!in_array($key, $hidden)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
