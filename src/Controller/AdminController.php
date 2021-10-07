<?php

namespace App\Controller;

use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/api/admin_check", name="admin_check")
     */
    public function index(): Response
    {
        $isAdmin = (new UserService())->isAdmin($this->getUser());
        return $this->json([
            "isAdmin" => $isAdmin
        ]);
    }
}
