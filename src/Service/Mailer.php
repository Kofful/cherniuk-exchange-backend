<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Mailer
{
    private MailerInterface $mailer;
    private TranslatorInterface $translator;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @param string $confirmPath
     * @param User $user
     * @throws TransportExceptionInterface
     */
    public function sendConfirmationEmail(string $confirmPath, User $user)
    {
        $html = $this->translator->trans("email.message", [], "email");
        $html = str_replace("%username%", $user->getUsername(), $html);
        $html = str_replace("%confirmPath%", $confirmPath, $html);

        $email = (new Email())
            ->from($_ENV["MAILER_FROM"])
            ->to($user->getEmail())
            ->subject($this->translator->trans('email.subject', [], "email"))
            ->html($html);

        $this->mailer->send($email);
    }
}
