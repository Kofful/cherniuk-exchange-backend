<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Service\Mailer;
use App\Service\Mapper\UserMapper;
use App\Service\Validator\UserValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class RegisterController extends AbstractController
{
    /**
     * @Route("/api/register", name="register")
     */
    public function index(ValidatorInterface $validator, MailerInterface $mailer): Response
    {
        $response = [];
        $request = Request::createFromGlobals();
        $post = $request->toArray();

        $user = (new UserMapper())->map($post);
        $errors = (new UserValidator())->validate($validator, $user);

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

            (new Mailer())->sendConfirmationEmail(
                $mailer,
                $this->generateUrl(
                    "confirm", ["code" => $user->getPassword(), "uid" => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $user);

            $response["code"] = 200;
        }

        return $this->json($response);
    }

    /**
     * @Route("/api/confirm", name="confirm")
     */
    public function confirmRegistration(Request $request): Response
    {
        return $this->json(["code"=>"200"]);
    }
}
