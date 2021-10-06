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

    public function validateConfirmation(array $query): array
    {
        $errors = [];
        if(!isset($query["code"])) {
            array_push($errors, "Confirmation code was not passed.");
        }
        if(!isset($query["uid"])) {
            array_push($errors, "UID was not passed.");
        }

        return $errors;
    }

    public function validateConfirmedUser(User $user, $code): array
    {
        $errors = [];

        if(!isset($user)) {
            array_push($errors, "User not found.");
        }
        if($user->getConfirmationCode() != $code) {
            array_push($errors, "Wrong confirmation code.");
        }

        return $errors;
    }
}
