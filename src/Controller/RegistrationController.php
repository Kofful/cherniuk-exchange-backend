<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use App\Service\Mapper\UserMapper;
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
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    /**
     * @Route("/api/register", name="register")
     */
    public function index(ValidatorInterface $validator, MailerInterface $mailer): Response
    {
        $response = [];
        $request = Request::createFromGlobals();
        $post = $request->toArray();

        $user = (new UserMapper())->map($post);
        $errors = (new RegistrationValidator())->validateUser($validator, $user);

        if(count($errors) > 0) {
            $response["code"] = 400;
            $response["messages"] = $errors;
        } else {
            $doctrine = $this->getDoctrine();

            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $role = $doctrine->getRepository(Role::class)->find(User::DEFAULT_ROLE_ID);
            $user->setRole($role);
            $status = $doctrine->getRepository(UserStatus::class)->find(User::DEFAULT_STATUS_ID);
            $user->setStatus($status);
            $user->setConfirmationCode((new CodeGenerator())->generate());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            (new Mailer())->sendConfirmationEmail(
                $mailer,
                $_ENV["FRONTEND_DOMAIN"] . "/confirm?code={$user->getConfirmationCode()}&uid={$user->getId()}",
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
        $response = [];

        $query = $request->query->all();
        $errors = (new RegistrationValidator())->validateConfirmation($query);

        if(count($errors) > 0) {
            $response["code"] = 400;
            $response["messages"] = $errors;
        } else {
            $doctrine = $this->getDoctrine();
            $user = $doctrine->getRepository(User::class)->find($query["uid"]);
            $errors = (new RegistrationValidator())->validateConfirmedUser($user, $query["code"]);
            if(count($errors) > 0) {
                $response["code"] = 400;
                $response["messages"] = $errors;
            } else {
                $status = $doctrine->getRepository(UserStatus::class)->find(User::CONFIRMED_STATUS_ID);
                $user->setStatus($status);
                $user->setConfirmationCode("");

                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $response["code"] = 200;
            }
        }
        return $this->json($response);
    }
}
