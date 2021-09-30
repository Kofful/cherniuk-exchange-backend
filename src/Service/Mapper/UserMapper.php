<?php

namespace App\Service\Mapper;

use App\Entity\User;

class UserMapper
{
    public function map(array $source): User
    {
        $user = new User();
        $user->setUsername($source["username"] ?? "");
        $user->setEmail($source["email"] ?? "");
        $user->setPassword(hash("sha256", $source["password"] ?? ""));

        return $user;
    }

}
