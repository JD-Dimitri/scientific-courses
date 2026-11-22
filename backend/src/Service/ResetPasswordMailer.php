<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * Génère un lien de réinitialisation et l'envoie par e-mail.
 * Utilisé par « Mot de passe oublié » et par l'administration.
 */
class ResetPasswordMailer
{
    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly MailerInterface $mailer,
        #[Autowire('%env(FRONTEND_URL)%')] private readonly string $frontendUrl,
        #[Autowire('%env(MAILER_FROM)%')] private readonly string $mailerFrom,
    ) {
    }

    /**
     * @throws ResetPasswordExceptionInterface si un lien a déjà été demandé il y a moins de 15 minutes
     * @throws TransportExceptionInterface     si l'e-mail n'a pas pu partir
     */
    public function send(User $user): void
    {
        $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerFrom, 'Scientific Courses'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->subject('Réinitialisation de ton mot de passe')
            ->htmlTemplate('email/reset_password.html.twig')
            ->context([
                'firstName' => $user->getFirstName(),
                'resetUrl' => rtrim($this->frontendUrl, '/').'/nouveau-mot-de-passe/'.$resetToken->getToken(),
                'lifetimeMinutes' => intdiv($this->resetPasswordHelper->getTokenLifetime(), 60),
            ]);

        $this->mailer->send($email);
    }
}
