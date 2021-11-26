<?php

namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateRewardedAt(User $user): void
    {
        $user->setRewardedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }
}
