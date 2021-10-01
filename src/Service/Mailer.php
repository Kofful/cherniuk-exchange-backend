<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Mailer
{

    /**
     * @param MailerInterface $mailer
     * @param string $confirmPath
     * @param User $user
     * @throws TransportExceptionInterface
     */
    public function sendConfirmationEmail(MailerInterface $mailer, string $confirmPath, User $user)
    {
        $email = (new Email())
            ->from('vlad26v03@gmail.com')
            ->to($user->getEmail())
            ->subject('Email confirmation')
            ->html('<h3>Hello, ' . $user->getUsername() . '!</h3>
<p>Confirm registration on Exchange by clicking on <a href="'. $confirmPath . '">this</a> link!<br></p>');

        $mailer->send($email);
    }
}