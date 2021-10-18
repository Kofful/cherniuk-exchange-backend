<?php

namespace App\Service\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateUser(User $user): array
    {
        $result = [];
        $errors = $this->validator->validate($user);

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
            array_push($errors, "confirmation.code.not.passed");
        }
        if(!isset($query["uid"])) {
            array_push($errors, "confirmation.uid.not.passed");
        }

        return $errors;
    }

    public function validateConfirmedUser(User $user, $code): array
    {
        $errors = [];

        if(!isset($user)) {
            array_push($errors, "user.not.found");
        }
        if($user->getConfirmationCode() != $code) {
            array_push($errors, "confirmation.code.wrong");
        }

        return $errors;
    }
}
