<?php

namespace App\Service\User;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    public function isAdmin(UserInterface $user): bool
    {
        $roles = $user->getRoles();
        $isAccepted = false;
        if(in_array(User::ADMIN_ROLE_NAME, $roles)) {
            $isAccepted = true;
        }
        return $isAccepted;
    }
}
