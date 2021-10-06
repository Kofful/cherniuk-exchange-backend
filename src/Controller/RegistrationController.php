<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use App\Service\Mapper\UserMapper;
use App\Service\Registration\ConfirmationService;
use App\Service\Registration\RegistrationService;
use App\Service\Validator\RegistrationValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class RegistrationController extends AbstractController
{
    /**
     * @Route("/api/register", name="register")
     */
    public function index(RegistrationService $registrationService): Response
    {
        $request = Request::createFromGlobals();
        $post = $request->toArray();
        $user = (new UserMapper())->map($post);

        $response = $registrationService->prepare($user);

        if(!isset($response["messages"])) {
            $response = $registrationService->register($user);
        }

        return $this->json($response);
    }

    /**
     * @Route("/api/confirm", name="confirm")
     */
    public function confirmRegistration(ConfirmationService $confirmationService, Request $request): Response
    {
        $response = $confirmationService->prepare($request->query->all());
        if(!isset($response["messages"])) {
            $response = $confirmationService->confirm($request->query->all());
        }
        return $this->json($response);
    }
}
