<?php

namespace App\Controller;

use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    public function index(UserService $userService): Response
    {
        $user = $userService->getUser($this->getUser());
        return $this->json([
            "user" => $user
        ]);
    }
}
