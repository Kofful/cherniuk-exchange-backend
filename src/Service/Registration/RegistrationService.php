<?php

namespace App\Service\Registration;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Repository\RoleRepository;
use App\Repository\UserStatusRepository;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use App\Service\Mapper\UserMapper;
use App\Service\Validator\RegistrationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationService
{
    private $passwordHasher;
    private $validator;
    private $roleRepository;
    private $userStatusRepository;
    private $entityManager;
    private $mailer;

    public function __construct(ValidatorInterface  $validator, UserPasswordHasherInterface $passwordHasher,
                                RoleRepository $roleRepository, UserStatusRepository $userStatusRepository,
                                EntityManagerInterface $entityManager, Mailer $mailer)
    {
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
        $this->roleRepository = $roleRepository;
        $this->userStatusRepository = $userStatusRepository;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    public function prepare(User $user): array
    {
        $response = [];
        $errors = (new RegistrationValidator())->validateUser($this->validator, $user);

        if(count($errors) > 0) {
            $response["code"] = 400;
            $response["messages"] = $errors;
        }

        return $response;
    }

    public function register(User $user): array
    {
        $response = [];

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        $role = $this->roleRepository->find(User::DEFAULT_ROLE_ID);
        $user->setRole($role);
        $status = $this->userStatusRepository->find(User::DEFAULT_STATUS_ID);
        $user->setStatus($status);
        $user->setConfirmationCode((new CodeGenerator())->generate());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        try {
            $this->mailer->sendConfirmationEmail(
                $_ENV["FRONTEND_DOMAIN"] . "/confirm?code={$user->getConfirmationCode()}&uid={$user->getId()}",
                $user);
            $response["code"] = 200;
        } catch(\Throwable $t) {
            $response["code"] = 500;
            $response["message"] = "Failed to send confirmation to email.";
        }
        return $response;
    }
}
