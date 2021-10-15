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
    private UserRepository $userRepository;
    private UserStatusRepository $userStatusRepository;
    private EntityManagerInterface $entityManager;
    private RegistrationValidator $registrationValidator;

    public function __construct(UserRepository $userRepository, UserStatusRepository $userStatusRepository,
                                EntityManagerInterface $entityManager, RegistrationValidator $registrationValidator) {
        $this->userRepository = $userRepository;
        $this->userStatusRepository = $userStatusRepository;
        $this->entityManager = $entityManager;
        $this->registrationValidator = $registrationValidator;
    }

    public function confirm(array $query): array
    {
        $result = [];
        $user = $this->userRepository->find($query["uid"]);

        if(!isset($user)) {
            return ["User not found."];
        }

        $errors = $this->registrationValidator->validateConfirmedUser($user, $query["code"]);

        if(count($errors) > 0) {
            $result = $errors;
        } else {
            $status = $this->userStatusRepository->find(User::CONFIRMED_STATUS_ID);
            $user->setStatus($status);
            $user->setConfirmationCode("");

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $result;
    }
}
