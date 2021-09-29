<?php

namespace App\Service\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Validation;

class UserValidator
{
    public function validate(User $user): array
    {
        $result = [];
        $validator = Validation::createValidator();
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            foreach($errors as $error) {
                array_push($result, $error->getMessage());
            }
        }

        return $result;
    }
}
