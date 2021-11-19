<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    public function dropSticker(Request $request): Response
    {
        $sticker = "test";
        return $this->json([
            "sticker" => $sticker
        ]);
    }
}
