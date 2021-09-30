<?php

namespace App\Service\Mailer;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserMailer
{
    public function sendEmail(MailerInterface $mailer, User $user)
    {
        $email = (new Email())
            ->from('vlad26v03@gmail.com')
            ->to($user->getEmail())
            ->subject('Email confirmation')
            ->text('Hello, ' . $user->getUsername() . '!')
            ->html('<p>Confirm registration on Exchange by clicking on <a href="'. $_ENV["APP_DOMAIN"] . '/confirm?code=' . $user->getPassword() . '">this</a> link!<br></p>');

        $mailer->send($email);
    }
}