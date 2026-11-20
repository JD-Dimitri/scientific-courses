<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AttemptRepository;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
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
}
