<?php

namespace App\Service\Registration;

use App\Entity\User;
use App\Entity\UserStatus;
use App\Repository\UserRepository;
use App\Repository\UserStatusRepository;
use App\Service\Validator\RegistrationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ConfirmationService
{
    private $userRepository;
    private $userStatusRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, UserStatusRepository $userStatusRepository, EntityManagerInterface $entityManager) {
        $this->userRepository = $userRepository;
        $this->userStatusRepository = $userStatusRepository;
        $this->entityManager = $entityManager;
    }

    public function prepare(array $query): array
    {
        $response = [];

        $errors = (new RegistrationValidator())->validateConfirmation($query);

        if(count($errors) > 0) {
            $response["code"] = 400;
            $response["messages"] = $errors;
        }

        return $response;
    }

    public function confirm(array $query): array
    {
        $response = [];
        $user = $this->userRepository->find($query["uid"]);
        $errors = (new RegistrationValidator())->validateConfirmedUser($user, $query["code"]);

        if(count($errors) > 0) {
            $response["code"] = 400;
            $response["messages"] = $errors;
        } else {
            $status = $this->userStatusRepository->find(User::CONFIRMED_STATUS_ID);
            $user->setStatus($status);
            $user->setConfirmationCode("");

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $response["code"] = 200;
        }

        return $response;
    }
}
