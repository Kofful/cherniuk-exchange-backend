<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\StatusCode;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    public function index(): Response
    {
        return $this->json([
            "user" => $this->getUser()
        ], StatusCode::STATUS_OK, [], ["groups" => "self"]);
    }

    public function getUserInfo(
        UserRepository $userRepository,
        TranslatorInterface $translator,
        Request $request
    ): Response {
        $status = StatusCode::STATUS_OK;

        $userId = $request->attributes->get("id");
        $user = $userRepository->find($userId);

        if (!isset($user)) {
            $status = StatusCode::STATUS_BAD_REQUEST;
            $user = [$translator->trans("user.not.found", [], "responses")];
        }

        return $this->json($user, $status, [], ["groups" => "profile"]);
    }
}
