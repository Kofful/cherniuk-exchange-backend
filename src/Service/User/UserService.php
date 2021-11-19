<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\Normalizer\Normalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUser(User $user): array
    {
        $userArray = (new Serializer([new ObjectNormalizer()]))->normalize($user);

        $userNormalizer = new Normalizer();
        $hiddenColumns = ["password", "createdAt", "updatedAt", "role", "status", "salt", "confirmationCode"];

        $result = $userNormalizer->normalize($userArray, $hiddenColumns);

        return $result;
    }

    public function updateRewardedAt(User $user): void
    {
        $user->setRewardedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }
}
