<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Service\Mapper\UserMapper;
use App\Service\Validator\UserValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RegisterController extends AbstractController
{
    /**
     * @Route("/api/register", name="register")
     */
    public function index(): Response
    {
        $response = [];
        $request = Request::createFromGlobals();
        $post = $request->toArray();

        $user = (new UserMapper())->map($post);
        $errors = (new UserValidator())->validate($user);

        if(count($errors) > 0) {
            $response["code"] = 400;
            $response["messages"] = $errors;
        } else {
            $doctrine = $this->getDoctrine();

            $role = $doctrine->getRepository(Role::class)->find(User::DEFAULT_ROLE_ID);
            $user->setRole($role);
            $status = $doctrine->getRepository(UserStatus::class)->find(User::DEFAULT_STATUS_ID);
            $user->setStatus($status);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $response["code"] = 200;
        }

        return $this->json($response);
    }
}
