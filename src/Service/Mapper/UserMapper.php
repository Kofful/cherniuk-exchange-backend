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
        $user->setPassword( $source["password"] ?? "");

        return $user;
    }

}
