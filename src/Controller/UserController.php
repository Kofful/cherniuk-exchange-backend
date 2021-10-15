<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/api/self", name="self")
     */
    public function index(UserService $userService): Response
    {
        $user = $userService->getUser($this->getUser());
        return $this->json([
            "user" => $user
        ]);
    }
}
