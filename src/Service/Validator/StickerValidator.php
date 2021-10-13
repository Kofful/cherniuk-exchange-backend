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

    public function validateSticker(Sticker $sticker): array
    {
        $result = [];
        $errors = $this->validator->validateProperty($sticker, "name");
        $errors->addAll($this->validator->validateProperty($sticker, "coefficient"));

        if (count($errors) > 0) {
            foreach($errors as $error) {
                array_push($result, $error->getMessage());
            }
        }

        return $result;
    }
}
