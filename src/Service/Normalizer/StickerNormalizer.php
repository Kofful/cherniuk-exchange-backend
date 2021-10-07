<?php

namespace App\Service\Normalizer;

class StickerNormalizer
{
    public function normalize(array $sticker, array $hidden = ["updated_at", "created_at"]): array
    {
        $result = [];

        foreach($sticker as $key => $value) {
            if(!in_array($key, $hidden)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
