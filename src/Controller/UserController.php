<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\StatusCode;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use const Grpc\STATUS_OK;

class UserController extends AbstractController
{
    public function index(UserService $userService): Response
    {
        $user = $userService->getUser($this->getUser());
        return $this->json([
            "user" => $user
        ]);
    }

    public function getUserInfo(UserRepository $userRepository, TranslatorInterface $translator, Request $request): Response
    {
        $response = [];
        $status = StatusCode::STATUS_OK;

        $userId = $request->attributes->get("id");
        $user = $userRepository->find($userId);

        if(isset($user)) {
            $response = [
                "id" => $user->getId(),
                "username" => $user->getUsername()
            ];
        } else {
            $status = StatusCode::STATUS_BAD_REQUEST;
            $response = [$translator->trans("user.not.found", [], "responses")];
        }

        return $this->json($response, $status);
    }
}
