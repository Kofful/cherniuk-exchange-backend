<?php

namespace App\Service\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationValidator
{
    public function validateUser(ValidatorInterface $validator, User $user): array
    {
        $result = [];
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            foreach($errors as $error) {
                array_push($result, $error->getMessage());
            }
        }

        return $result;
    }
}
