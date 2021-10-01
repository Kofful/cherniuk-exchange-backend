<?php

namespace App\Service\Registration;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use App\Service\Mapper\UserMapper;
use App\Service\Validator\RegistrationValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationService
{
    public function prepare(User $user, ValidatorInterface $validator): array
    {
        $response = [];
        $errors = (new RegistrationValidator())->validateUser($validator, $user);

        if(count($errors) > 0) {
            $response["code"] = 400;
            $response["messages"] = $errors;
        }

        return $response;
    }

    public function register(User $user, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): array
    {
        $response = [];

        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $role = $doctrine->getRepository(Role::class)->find(User::DEFAULT_ROLE_ID);
        $user->setRole($role);
        $status = $doctrine->getRepository(UserStatus::class)->find(User::DEFAULT_STATUS_ID);
        $user->setStatus($status);
        $user->setConfirmationCode((new CodeGenerator())->generate());

        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        try {
            (new Mailer())->sendConfirmationEmail(
                $mailer,
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
