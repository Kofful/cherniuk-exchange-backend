<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use App\Service\Registration\ConfirmationService;
use App\Service\Registration\RegistrationService;
use App\Service\StatusCode;
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
    public function index(
        RegistrationService $registrationService,
        RegistrationValidator $registrationValidator,
        Request $request
    ): Response {
        $status = StatusCode::STATUS_OK;
        $body = [];

        $user = $registrationService->prepareUser($request->toArray());
        $errors = $registrationValidator->validateUser($user);

        if (count($errors) > 0) {
            $status = StatusCode::STATUS_BAD_REQUEST;
            $body = $errors;
        } else {
            $body = $registrationService->register($user);
        }

        return $this->json($body, $status);
    }

    public function confirmRegistration(
        ConfirmationService $confirmationService,
        RegistrationValidator $registrationValidator,
        Request $request
    ): Response {
        $status = StatusCode::STATUS_OK;
        $body = [];

        $errors = $registrationValidator->validateConfirmation($request->query->all());

        if (count($errors) > 0) {
            $status = StatusCode::STATUS_BAD_REQUEST;
            $body = $errors;
        } else {
            $body = $confirmationService->confirm($request->query->all());
            if (count($body) > 0) {
                $status = StatusCode::STATUS_BAD_REQUEST;
            }
        }

        return $this->json($body, $status);
    }
}
