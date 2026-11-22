<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AttemptRepository;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use App\Service\ResetPasswordMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Administration. Réservée aux comptes ayant le rôle ROLE_ADMIN
 * (contrôle aussi fait dans security.yaml pour tout /api/admin).
 */
#[Route('/api/admin')]
#[IsGranted(User::ROLE_ADMIN)]
class AdminController extends AbstractController
{
    /** La liste des comptes et la progression de chacun. */
    #[Route('/students', name: 'api_admin_students', methods: ['GET'])]
    public function students(UserRepository $users, AttemptRepository $attempts, QuestionRepository $questions): JsonResponse
    {
        $summary = $attempts->summaryByUser();
        $questionCount = $questions->count([]);

        $data = array_map(function (User $user) use ($summary) {
            $s = $summary[$user->getId()] ?? ['answered' => 0, 'correct' => 0, 'lastActivity' => null];

            return $user->toArray() + [
                'createdAt' => $user->getCreatedAt()->format(\DATE_ATOM),
                'answered' => $s['answered'],
                'correct' => $s['correct'],
                'lastActivity' => $s['lastActivity'] instanceof \DateTimeInterface
                    ? $s['lastActivity']->format(\DATE_ATOM)
                    : $s['lastActivity'],
            ];
        }, $users->findStudents());

        return $this->json(['questionCount' => $questionCount, 'students' => $data]);
    }

    /** Envoie à un élève un lien de réinitialisation de mot de passe par e-mail. */
    #[Route('/students/{id}/reset-link', name: 'api_admin_reset_link', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function sendResetLink(int $id, UserRepository $users, ResetPasswordMailer $resetMailer): JsonResponse
    {
        $user = $users->find($id);
        if (null === $user) {
            return $this->json(['error' => 'Compte introuvable.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $resetMailer->send($user);
        } catch (ResetPasswordExceptionInterface) {
            return $this->json(
                ['error' => 'Un lien a déjà été envoyé à ce compte il y a moins de 15 minutes.'],
                Response::HTTP_TOO_MANY_REQUESTS,
            );
        } catch (TransportExceptionInterface) {
            return $this->json(
                ['error' => 'L\'e-mail n\'a pas pu partir. Vérifie que Mailpit (ou le service d\'envoi) fonctionne.'],
                Response::HTTP_SERVICE_UNAVAILABLE,
            );
        }

        return $this->json(['message' => sprintf('Lien de réinitialisation envoyé à %s.', $user->getEmail())]);
    }
}
