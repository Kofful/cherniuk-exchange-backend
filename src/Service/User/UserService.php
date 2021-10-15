<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\Normalizer\Normalizer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserService
{
    public function getUser(User $user)
    {
        $userArray = (new Serializer([new ObjectNormalizer()]))->normalize($user);

        $userNormalizer = new Normalizer();
        $hiddenColumns = ["password", "createdAt", "updatedAt", "role", "status", "salt", "confirmationCode"];

        $result = $userNormalizer->normalize($userArray, $hiddenColumns);

        return $result;
    }
}
