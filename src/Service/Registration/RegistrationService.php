<?php

namespace App\Service\Registration;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Repository\RoleRepository;
use App\Repository\UserStatusRepository;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use App\Service\Validator\RegistrationValidator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationService
{
    private UserPasswordHasherInterface $passwordHasher;
    private RoleRepository $roleRepository;
    private UserStatusRepository $userStatusRepository;
    private EntityManagerInterface $entityManager;
    private Mailer $mailer;

    public function __construct(UserPasswordHasherInterface $passwordHasher,
                                RoleRepository $roleRepository, UserStatusRepository $userStatusRepository,
                                EntityManagerInterface $entityManager, Mailer $mailer)
    {
        $this->passwordHasher = $passwordHasher;
        $this->roleRepository = $roleRepository;
        $this->userStatusRepository = $userStatusRepository;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    public function prepareUser(array $query): User
    {
        return (new Serializer([new ObjectNormalizer()]))
            ->denormalize($query,"App\Entity\User");
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
        } catch(\Throwable $t) {
            $response["message"] = "Failed to send confirmation to email.";
        }
        return $response;
    }
}
