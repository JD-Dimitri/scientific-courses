<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * Mot de passe oublié, en deux étapes :
 * 1. POST /api/reset-password/request {"email"} : envoie un lien par e-mail.
 * 2. POST /api/reset-password/reset {"token", "password"} : enregistre le nouveau mot de passe.
 */
#[Route('/api/reset-password')]
class ResetPasswordController extends AbstractController
{
    use JsonBodyTrait;

    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/request', name: 'api_reset_password_request', methods: ['POST'])]
    public function request(
        Request $request,
        UserRepository $users,
        MailerInterface $mailer,
        #[Autowire('%env(FRONTEND_URL)%')] string $frontendUrl,
        #[Autowire('%env(MAILER_FROM)%')] string $mailerFrom,
    ): JsonResponse {
        $email = (string) ($this->jsonBody($request)['email'] ?? '');

        // Même réponse que le compte existe ou non : on ne révèle pas quelles adresses sont inscrites.
        $response = $this->json(
            ['message' => 'Si un compte existe avec cette adresse, un e-mail de réinitialisation vient d\'être envoyé.'],
            Response::HTTP_ACCEPTED,
        );

        $user = '' !== $email ? $users->findOneByEmail($email) : null;
        if (null === $user) {
            return $response;
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // Par exemple une demande déjà faite il y a moins de 15 minutes.
            $this->logger->info('Demande de réinitialisation refusée : {reason}', ['reason' => $e->getReason()]);

            return $response;
        }

        $email = (new TemplatedEmail())
            ->from(new Address($mailerFrom, 'Scientific Courses'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->subject('Réinitialisation de ton mot de passe')
            ->htmlTemplate('email/reset_password.html.twig')
            ->context([
                'firstName' => $user->getFirstName(),
                'resetUrl' => rtrim($frontendUrl, '/').'/nouveau-mot-de-passe/'.$resetToken->getToken(),
                'lifetimeMinutes' => intdiv($this->resetPasswordHelper->getTokenLifetime(), 60),
            ]);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Envoi de l\'e-mail de réinitialisation impossible : {error}', ['error' => $e->getMessage()]);
        }

        return $response;
    }

    #[Route('/reset', name: 'api_reset_password_reset', methods: ['POST'])]
    public function reset(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em,
    ): JsonResponse {
        $data = $this->jsonBody($request);
        $token = (string) ($data['token'] ?? '');
        $plainPassword = (string) ($data['password'] ?? '');

        if (mb_strlen($plainPassword) < AuthController::PASSWORD_MIN_LENGTH) {
            return $this->json(
                ['errors' => ['password' => sprintf('Le mot de passe doit contenir au moins %d caractères.', AuthController::PASSWORD_MIN_LENGTH)]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        try {
            /** @var \App\Entity\User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface) {
            return $this->json(
                ['error' => 'Ce lien n\'est plus valable. Refais une demande de réinitialisation.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        // Le lien ne sert qu'une fois.
        $this->resetPasswordHelper->removeResetRequest($token);

        $user->setPassword($hasher->hashPassword($user, $plainPassword));
        $em->flush();

        return $this->json(['message' => 'Ton mot de passe a été modifié. Tu peux te connecter.']);
    }
}
