<?php

namespace App\Service\Registration;

use App\Entity\User;
use App\Entity\UserStatus;
use App\Service\Validator\RegistrationValidator;
use Doctrine\Persistence\ManagerRegistry;

class ConfirmationService
{
    public function prepare(array $query, ManagerRegistry $doctrine): array
    {
        $response = [];

        $errors = (new RegistrationValidator())->validateConfirmation($query);

        if(count($errors) > 0) {
            $response["code"] = 400;
            $response["messages"] = $errors;
        }

        return $response;
    }

    public function confirm(array $query, ManagerRegistry $doctrine): array
    {
        $response = [];
        $user = $doctrine->getRepository(User::class)->find($query["uid"]);
        $errors = (new RegistrationValidator())->validateConfirmedUser($user, $query["code"]);

        if(count($errors) > 0) {
            $response["code"] = 400;
            $response["messages"] = $errors;
        } else {
            $status = $doctrine->getRepository(UserStatus::class)->find(User::CONFIRMED_STATUS_ID);
            $user->setStatus($status);
            $user->setConfirmationCode("");

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $response["code"] = 200;
        }

        return $response;
    }
}
