<?php

namespace App\Service\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationValidator
{
    private ValidatorInterface $validator;
    private TranslatorInterface $translator;

    public function __construct(ValidatorInterface $validator, TranslatorInterface $translator)
    {
        $this->validator = $validator;
        $this->translator = $translator;
    }

    public function validateUser(User $user): array
    {
        $result = [];
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                array_push($result, $error->getMessage());
            }
        }

        return $result;
    }

    public function validateConfirmation(array $query): array
    {
        $errors = [];
        if (!isset($query["code"])) {
            array_push($errors, $this->translator->trans("confirmation.code.not.passed", [], "validators"));
        }
        if (!isset($query["uid"])) {
            array_push($errors, $this->translator->trans("confirmation.uid.not.passed", [], "validators"));
        }

        return $errors;
    }

    public function validateConfirmedUser(User $user, $code): array
    {
        $errors = [];

        if (!isset($user)) {
            array_push($errors, $this->translator->trans("user.not.found", [], "validators"));
        }
        if ($user->getConfirmationCode() != $code) {
            array_push($errors, $this->translator->trans("confirmation.code.wrong", [], "validators"));
        }

        return $errors;
    }
}
