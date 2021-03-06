<?php

namespace App\Service\Validator;

use App\Entity\User;

class OfferValidator extends Validator
{
    private function validatePage($page): array
    {
        $isValid = is_numeric($page) && $page > 0;
        $errors = $isValid ? [] : [$this->translator->trans("invalid.page", [], "responses")];
        return $errors;
    }

    private function validatePayment($payment): array
    {
        $isValid = is_numeric($payment) && $payment >= 0;
        $errors = $isValid ? [] : [$this->translator->trans("invalid.payment", [], "responses")];
        return $errors;
    }

    private function validateUser($user): array
    {
        $isValid = isset($user);
        $errors = $isValid ? [] : [$this->translator->trans("user.not.found", [], "responses")];
        return $errors;
    }

    public function validateIncoming($page): array
    {
        return $this->validatePage($page);
    }

    public function validateUserOffers($page, $user): array
    {
        $errors = $this->validatePage($page);
        $errors = array_merge($errors, $this->validateUser($user));
        return $errors;
    }

    public function validateGettingOfferQuery(array $query): array
    {
        $errors = $this->validatePage($query["page"]);
        $payments = [
            $query["minTargetPayment"],
            $query["maxTargetPayment"],
            $query["minCreatorPayment"],
            $query["maxCreatorPayment"]
        ];
        foreach ($payments as $payment) {
            $errors = array_merge($errors, $this->validatePayment($payment));
        }
        return $errors;
    }
}
