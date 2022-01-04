<?php

namespace App\Controller;

use App\Repository\UserRepository;
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
        ], Response::HTTP_OK, [], ["groups" => "self"]);
    }

    public function getUserInfo(
        UserRepository $userRepository,
        TranslatorInterface $translator,
        Request $request
    ): Response {
        $status = Response::HTTP_OK;

        $userId = $request->attributes->get("id");
        $user = $userRepository->find($userId);

        if (!isset($user)) {
            $status = Response::HTTP_BAD_REQUEST;
            $user = [$translator->trans("user.not.found", [], "responses")];
        }

        return $this->json($user, $status, [], ["groups" => "profile"]);
    }
}
