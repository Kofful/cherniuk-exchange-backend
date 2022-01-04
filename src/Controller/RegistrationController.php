<?php

namespace App\Controller;

use App\Service\Registration\ConfirmationService;
use App\Service\Registration\RegistrationService;
use App\Service\Validator\RegistrationValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends AbstractController
{
    public function index(
        RegistrationService $registrationService,
        RegistrationValidator $registrationValidator,
        Request $request
    ): Response {
        $status = Response::HTTP_OK;
        $body = [];

        $user = $registrationService->prepareUser($request->toArray());
        $errors = $registrationValidator->validateUser($user);

        if (count($errors) > 0) {
            $status = Response::HTTP_BAD_REQUEST;
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
        $status = Response::HTTP_OK;
        $body = [];

        $errors = $registrationValidator->validateConfirmation($request->query->all());

        if (count($errors) > 0) {
            $status = Response::HTTP_BAD_REQUEST;
            $body = $errors;
        } else {
            $body = $confirmationService->confirm($request->query->all());
            if (count($body) > 0) {
                $status = Response::HTTP_BAD_REQUEST;
            }
        }

        return $this->json($body, $status);
    }
}
