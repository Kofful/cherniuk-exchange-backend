<?php

namespace App\Service\Validator;

use App\Entity\Sticker;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StickerValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateSticker(Sticker $sticker, array $properties = ["name", "coefficient"]): array
    {
        $result = [];

        foreach($properties as $property) {
            $errors = $this->validator->validateProperty($sticker, $property);

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    array_push($result, $error->getMessage());
                }
            }
        }

        return $result;
    }
}
